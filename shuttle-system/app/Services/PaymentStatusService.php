<?php

namespace App\Services;

use App\Mail\TicketMail;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentStatusService
{
    public function __construct(private DompetXService $dompetX)
    {
    }

    public function syncBookingPayment(Booking $booking): Booking
    {
        $booking->loadMissing(['payment', 'customer.user']);
        $payment = $booking->payment;

        if (!$payment || $payment->gateway !== 'dompetx' || !$payment->gateway_transaction_id) {
            return $booking;
        }

        if ($payment->status === 'success') {
            $this->markBookingPaid($booking);
            return $booking->refresh();
        }

        if ($payment->status !== 'pending') {
            return $booking;
        }

        try {
            $response = $this->dompetX->checkCheckoutStatus($payment->gateway_transaction_id);
        } catch (\Throwable $e) {
            Log::warning('Failed to sync DompetX payment status: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'gateway_transaction_id' => $payment->gateway_transaction_id,
            ]);

            return $booking;
        }

        $status = $this->normalizeGatewayStatus(
            data_get($response, 'status')
            ?? data_get($response, 'data.status')
            ?? data_get($response, 'transaction.status')
            ?? 'pending'
        );

        $payment->update([
            'status' => $status,
            'gateway_response' => array_merge($payment->gateway_response ?? [], [
                'status_check' => $response,
            ]),
            'payment_time' => $status === 'success'
                ? ($payment->payment_time ?? now())
                : $payment->payment_time,
        ]);

        if ($status === 'success') {
            $this->markBookingPaid($booking);
        }

        return $booking->refresh();
    }

    public function updateFromGatewayPayload(Booking $booking, array $payload): Payment
    {
        $booking->loadMissing('payment');
        $status = $this->normalizeGatewayStatus(
            $payload['status'] ?? data_get($payload, 'data.status') ?? 'pending'
        );

        $payment = Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $payload['amount'] ?? data_get($payload, 'data.amount') ?? $booking->total_price,
                'payment_method' => $payload['method'] ?? data_get($payload, 'data.method') ?? optional($booking->payment)->payment_method ?? 'dompetx',
                'gateway' => 'dompetx',
                'gateway_transaction_id' => $payload['id'] ?? data_get($payload, 'data.id') ?? optional($booking->payment)->gateway_transaction_id,
                'payment_url' => optional($booking->payment)->payment_url,
                'gateway_response' => $payload,
                'status' => $status,
                'payment_time' => $status === 'success' ? now() : optional($booking->payment)->payment_time,
            ]
        );

        if ($status === 'success') {
            $this->markBookingPaid($booking);
        }

        return $payment;
    }

    public function markBookingPaid(Booking $booking): void
    {
        $booking->loadMissing(['customer.user']);

        if ($booking->status !== 'paid') {
            $booking->update(['status' => 'paid']);
            $booking->status = 'paid';

            $this->sendTicketEmail($booking);
        }
    }

    public function normalizeGatewayStatus(string $status): string
    {
        return match (strtolower($status)) {
            'paid', 'success', 'successful', 'completed', 'settled' => 'success',
            'failed', 'failure', 'expired', 'cancelled', 'canceled' => 'failed',
            default => 'pending',
        };
    }

    private function sendTicketEmail(Booking $booking): void
    {
        try {
            $emailTarget = $booking->passenger_email ?? $booking->customer->user->email;

            if (!$emailTarget) {
                Log::warning('Ticket email skipped because target email is empty.', [
                    'booking_id' => $booking->id,
                ]);

                return;
            }

            Mail::to($emailTarget)->send(new TicketMail($booking));
        } catch (\Throwable $e) {
            Log::error('Failed to send ticket email: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
            ]);
        }
    }
}
