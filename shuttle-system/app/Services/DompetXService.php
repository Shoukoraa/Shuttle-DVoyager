<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class DompetXService
{
    public function createCheckout(array $payload, ?string $idempotencyKey = null): array
    {
        $body = $this->jsonBody($payload);

        return $this->client($body, $idempotencyKey)
            ->withBody($body, 'application/json')
            ->post('/payments/checkout')
            ->throw()
            ->json();
    }

    public function checkCheckoutStatus(string $checkoutId): array
    {
        return $this->client('{}')
            ->get("/payments/checkout/{$checkoutId}/check-status")
            ->throw()
            ->json();
    }

    public function verifyWebhookSignature(string $rawBody, ?string $signature, ?string $timestamp): bool
    {
        if (!$signature || !$timestamp) {
            return false;
        }

        return hash_equals($this->signature($timestamp, $rawBody), $signature);
    }

    private function client(string $body, ?string $idempotencyKey = null)
    {
        $apiKey = (string) config('services.dompetx.api_key');

        if ($apiKey === '') {
            throw new RuntimeException('DOMPETX_API_KEY belum dikonfigurasi di backend.');
        }

        $timestamp = (string) time();
        $headers = [
            'Content-Type' => 'application/json',
            'X-DOMPAY-API-Key' => $apiKey,
            'X-DOMPAY-Signature' => $this->signature($timestamp, $body),
            'X-DOMPAY-Timestamp' => $timestamp,
        ];

        if ($idempotencyKey) {
            $headers['Idempotency-Key'] = $idempotencyKey;
        }

        return Http::baseUrl(rtrim((string) config('services.dompetx.base_url'), '/'))
            ->acceptJson()
            ->withHeaders($headers)
            ->timeout(20);
    }

    private function jsonBody(array $payload): string
    {
        return json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function signature(string $timestamp, string $body): string
    {
        return hash_hmac(
            'sha256',
            $timestamp . '.' . $body,
            (string) config('services.dompetx.api_key')
        );
    }

    public static function idempotencyKey(int $bookingId): string
    {
        return 'dvoyager_booking_' . $bookingId . '_' . Str::uuid();
    }
}
