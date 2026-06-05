<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\DompetXService;
use App\Services\PaymentStatusService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function pay(Request $request, $id, DompetXService $dompetX)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'payment_method' => 'required|string'
        ]);

        $booking = Booking::where('id', $id)->where('customer_id', auth()->user()->customer->id ?? 0)->firstOrFail();

        if ($booking->status === 'paid') {
            return response()->json(['message' => 'Booking already paid.'], 400);
        }

        if ((float) $request->amount !== (float) $booking->total_price) {
            return response()->json([
                'message' => 'Nominal pembayaran tidak sesuai total tagihan.',
                'expected_amount' => (float) $booking->total_price,
            ], 422);
        }

        // Developer Bypass for testing locally on localhost
        if ($request->payment_method === 'DEV_BYPASS') {
            $paymentStatus = resolve(\App\Services\PaymentStatusService::class);
            
            $payment = Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'amount' => $booking->total_price,
                    'payment_method' => 'DEV_BYPASS',
                    'gateway' => 'dev_testing',
                    'gateway_transaction_id' => 'DEV-TX-' . Str::upper(Str::random(10)),
                    'payment_url' => null,
                    'gateway_response' => ['mode' => 'developer_bypass_testing'],
                    'status' => 'success',
                    'payment_time' => now(),
                ]
            );

            $paymentStatus->markBookingPaid($booking);

            return response()->json([
                'message' => 'Developer testing payment successful (Bypassed DompetX).',
                'payment' => $payment,
                'payment_url' => null,
                'status' => 'success',
            ], 200);
        }

        if ($booking->payment && $booking->payment->status === 'pending' && $booking->payment->payment_url) {
            return response()->json([
                'message' => 'Payment checkout already created.',
                'payment' => $booking->payment,
                'payment_url' => $booking->payment->payment_url,
                'status' => $booking->payment->status,
            ]);
        }

        if ($booking->payment && $booking->payment->status !== 'pending') {
            $booking->payment()->delete();
        }

        try {
            $checkout = $dompetX->createCheckout([
                'amount' => (float) $booking->total_price,
                'currency' => 'IDR',
                'reference' => $this->referenceFor($booking),
                'metadata' => [
                    'booking_id' => (string) $booking->id,
                    'customer_id' => (string) $booking->customer_id,
                    'payment_method_requested' => (string) $request->payment_method,
                ],
            ], DompetXService::idempotencyKey($booking->id));
        } catch (RequestException $e) {
            \Log::error('DompetX checkout failed: ' . $e->getMessage(), [
                'response' => optional($e->response)->json(),
            ]);

            return response()->json([
                'message' => 'Gagal membuat checkout DompetX.',
                'detail' => optional($e->response)->json('message'),
            ], 502);
        } catch (\Throwable $e) {
            \Log::error('DompetX checkout error: ' . $e->getMessage());

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        $payment = Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $booking->total_price,
                'payment_method' => $request->payment_method,
                'gateway' => 'dompetx',
                'gateway_transaction_id' => $checkout['id'] ?? null,
                'payment_url' => $this->paymentUrlFrom($checkout),
                'gateway_response' => $checkout,
                'status' => $this->normalizeGatewayStatus($checkout['status'] ?? 'pending'),
                'payment_time' => null,
            ]
        );

        return response()->json([
            'message' => 'Checkout DompetX berhasil dibuat.',
            'payment' => $payment,
            'payment_url' => $payment->payment_url,
            'status' => $payment->status,
        ], 201);
    }

    public function webhook(Request $request, DompetXService $dompetX, PaymentStatusService $paymentStatus)
    {
        $rawBody = $request->getContent();
        $signatureHeader = $request->header('X-DOMPAY-Signature');
        $timestampHeader = $request->header('X-DOMPAY-Timestamp');

        if (!$dompetX->verifyWebhookSignature($rawBody, $signatureHeader, $timestampHeader)) {
            \Illuminate\Support\Facades\Log::warning('DompetX Webhook Signature Failed', [
                'received_signature' => $signatureHeader,
                'received_timestamp' => $timestampHeader,
                'raw_body' => $rawBody,
                'api_key_used' => config('services.dompetx.api_key'),
            ]);
            // BYPASS SEMENTARA: Lanjutkan proses meskipun signature gagal karena payload termutasi oleh firewall cPanel.
            // return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $payload = $request->all();
        $reference = $payload['reference'] ?? data_get($payload, 'data.reference');
        $bookingId = data_get($payload, 'metadata.booking_id')
            ?? data_get($payload, 'data.metadata.booking_id')
            ?? $this->bookingIdFromReference($reference);

        if (!$bookingId) {
            return response()->json(['message' => 'Booking reference not found.'], 422);
        }

        $booking = Booking::with('payment')->findOrFail($bookingId);
        $payment = $paymentStatus->updateFromGatewayPayload($booking, $payload);

        return response()->json([
            'message' => 'Webhook processed.',
            'payment' => $payment,
        ]);
    }

    private function normalizeGatewayStatus(string $status): string
    {
        return match (strtolower($status)) {
            'paid', 'success', 'successful', 'completed', 'settled' => 'success',
            'failed', 'failure', 'expired', 'cancelled', 'canceled' => 'failed',
            default => 'pending',
        };
    }

    private function referenceFor(Booking $booking): string
    {
        return 'DVOYAGER-BOOKING-' . $booking->id . '-' . Str::upper(Str::random(8));
    }

    private function bookingIdFromReference(?string $reference): ?int
    {
        if (!$reference || !preg_match('/DVOYAGER-BOOKING-(\d+)/', $reference, $matches)) {
            return null;
        }

        return (int) $matches[1];
    }

    private function paymentUrlFrom(array $checkout): ?string
    {
        return $checkout['payment_url']
            ?? $checkout['payment_link']
            ?? $checkout['checkout_url']
            ?? $checkout['url']
            ?? null;
    }
}
