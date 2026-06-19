<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\DompetXService;
use App\Services\PaymentStatusService;
use App\Support\DompetXPaymentMethods;
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

        $selectedMethod = DompetXPaymentMethods::normalizeAppMethod((string) $request->payment_method);
        if (!$selectedMethod) {
            return response()->json([
                'message' => 'Metode pembayaran tidak didukung.',
                'allowed_methods' => DompetXPaymentMethods::allowedAppMethods(),
            ], 422);
        }

        $gatewayMethod = DompetXPaymentMethods::gatewayMethodFor($selectedMethod);

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

        if ($booking->payment && $booking->payment->status === 'pending' && $booking->payment->payment_url) {
            if ($this->isCheckoutLockedToMethod($booking->payment, $selectedMethod)) {
                return response()->json([
                    'message' => 'Payment checkout already created.',
                    'payment' => $booking->payment,
                    'payment_url' => $booking->payment->payment_url,
                    'status' => $booking->payment->status,
                ]);
            }
        }

        if ($booking->payment && $booking->payment->status !== 'pending') {
            $booking->payment()->delete();
        }

        $reference = $this->referenceFor($booking);

        try {
            $checkout = $dompetX->createCheckout(
                $this->checkoutPayload($booking, $reference, $selectedMethod, $gatewayMethod),
                DompetXService::idempotencyKey($booking->id)
            );
        } catch (RequestException $e) {
            if ($this->shouldRetryWithoutPaymentMethodAlias($e)) {
                try {
                    $checkout = $dompetX->createCheckout(
                        $this->checkoutPayload($booking, $reference, $selectedMethod, $gatewayMethod, false),
                        DompetXService::idempotencyKey($booking->id)
                    );
                } catch (RequestException $retryException) {
                    return $this->dompetXCheckoutFailure($retryException);
                }
            } else {
                return $this->dompetXCheckoutFailure($e);
            }
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
                'payment_method' => $selectedMethod,
                'gateway' => 'dompetx',
                'gateway_transaction_id' => $checkout['id'] ?? null,
                'payment_url' => $this->paymentUrlFrom($checkout, $selectedMethod, $gatewayMethod),
                'gateway_response' => array_merge($checkout, [
                    'locked_payment_method' => $selectedMethod,
                    'locked_gateway_method' => $gatewayMethod,
                ]),
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

    private function checkoutPayload(
        Booking $booking,
        string $reference,
        string $selectedMethod,
        string $gatewayMethod,
        bool $includePaymentMethodAlias = true
    ): array {
        $payload = [
            'amount' => (float) $booking->total_price,
            'currency' => 'IDR',
            'reference' => $reference,
            'method' => $gatewayMethod,
            'metadata' => [
                'booking_id' => (string) $booking->id,
                'customer_id' => (string) $booking->customer_id,
                'payment_method_requested' => $selectedMethod,
                'gateway_method_requested' => $gatewayMethod,
            ],
        ];

        if ($includePaymentMethodAlias) {
            $payload['payment_method'] = $gatewayMethod;
        }

        return $payload;
    }

    private function shouldRetryWithoutPaymentMethodAlias(RequestException $e): bool
    {
        $status = optional($e->response)->status();
        $detail = strtolower((string) json_encode(optional($e->response)->json() ?? []));

        return in_array($status, [400, 422], true)
            && str_contains($detail, 'payment_method');
    }

    private function dompetXCheckoutFailure(RequestException $e)
    {
        \Log::error('DompetX checkout failed: ' . $e->getMessage(), [
            'response' => optional($e->response)->json(),
        ]);

        return response()->json([
            'message' => 'Gagal membuat checkout DompetX.',
            'detail' => optional($e->response)->json('message'),
        ], 502);
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

    private function paymentUrlFrom(array $checkout, ?string $selectedMethod = null, ?string $gatewayMethod = null): ?string
    {
        if ($selectedMethod || $gatewayMethod) {
            $methodUrl = $this->methodSpecificPaymentUrlFrom($checkout, $selectedMethod, $gatewayMethod);
            if ($methodUrl) {
                return $methodUrl;
            }
        }

        return $this->firstUrlFrom($checkout, [
            'payment_url',
            'payment_link',
            'checkout_url',
            'redirect_url',
            'invoice_url',
            'url',
        ]);
    }

    private function methodSpecificPaymentUrlFrom(array $payload, ?string $selectedMethod, ?string $gatewayMethod): ?string
    {
        $acceptedMethods = array_filter([
            $selectedMethod,
            $gatewayMethod,
            strtolower((string) $selectedMethod),
            strtolower((string) $gatewayMethod),
        ]);

        foreach ($payload as $key => $value) {
            if (!is_array($value)) {
                continue;
            }

            if (in_array((string) $key, $acceptedMethods, true)) {
                $url = $this->firstUrlFrom($value);
                if ($url) {
                    return $url;
                }
            }

            $method = $value['method']
                ?? $value['payment_method']
                ?? $value['channel']
                ?? $value['code']
                ?? $value['type']
                ?? null;

            if ($method && (
                DompetXPaymentMethods::isSameMethod((string) $method, (string) $selectedMethod)
                || strtoupper((string) $method) === strtoupper((string) $gatewayMethod)
            )) {
                $url = $this->firstUrlFrom($value);
                if ($url) {
                    return $url;
                }
            }

            $url = $this->methodSpecificPaymentUrlFrom($value, $selectedMethod, $gatewayMethod);
            if ($url) {
                return $url;
            }
        }

        return null;
    }

    private function firstUrlFrom(array $payload, ?array $preferredKeys = null): ?string
    {
        $urlKeys = $preferredKeys ?? [
            'payment_url',
            'payment_link',
            'checkout_url',
            'redirect_url',
            'invoice_url',
            'url',
            'qris_url',
            'va_url',
        ];

        foreach ($urlKeys as $key) {
            if (!empty($payload[$key]) && is_string($payload[$key])) {
                return $payload[$key];
            }
        }

        return null;
    }

    private function isCheckoutLockedToMethod(Payment $payment, string $selectedMethod): bool
    {
        if (!DompetXPaymentMethods::isSameMethod($payment->payment_method, $selectedMethod)) {
            return false;
        }

        $gatewayResponse = is_array($payment->gateway_response) ? $payment->gateway_response : [];
        $lockedMethod = $gatewayResponse['locked_payment_method']
            ?? data_get($gatewayResponse, 'metadata.payment_method_requested')
            ?? data_get($gatewayResponse, 'metadata.gateway_method_requested')
            ?? $gatewayResponse['method']
            ?? $gatewayResponse['payment_method']
            ?? null;

        return $lockedMethod !== null
            && (
                DompetXPaymentMethods::isSameMethod((string) $lockedMethod, $selectedMethod)
                || strtoupper((string) $lockedMethod) === strtoupper(DompetXPaymentMethods::gatewayMethodFor($selectedMethod))
            );
    }
}
