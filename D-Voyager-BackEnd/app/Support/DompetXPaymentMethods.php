<?php

namespace App\Support;

class DompetXPaymentMethods
{
    private const APP_TO_GATEWAY = [
        'VA_PERMATA' => 'PERMATAVA',
        'VA_BCA' => 'BCAVA',
        'VA_MANDIRI' => 'MANDIRIVA',
        'VA_DANAMON' => 'DANAMONVA',
        'VA_CIMB' => 'CIMBVA',
        'VA_BSI' => 'BSIVA',
        'VA_BRI' => 'BRIVA',
        'VA_BNI' => 'BNIVA',
        'QRIS' => 'QRIS',
    ];

    private const ALIASES = [
        'PERMATAVA' => 'VA_PERMATA',
        'PERMATA_VA' => 'VA_PERMATA',
        'VA_PERMATA' => 'VA_PERMATA',
        'BCAVA' => 'VA_BCA',
        'BCA_VA' => 'VA_BCA',
        'VA_BCA' => 'VA_BCA',
        'MANDIRIVA' => 'VA_MANDIRI',
        'MANDIRI_VA' => 'VA_MANDIRI',
        'VA_MANDIRI' => 'VA_MANDIRI',
        'DANAMONVA' => 'VA_DANAMON',
        'DANAMON_VA' => 'VA_DANAMON',
        'VA_DANAMON' => 'VA_DANAMON',
        'CIMBVA' => 'VA_CIMB',
        'CIMB_VA' => 'VA_CIMB',
        'VA_CIMB' => 'VA_CIMB',
        'BSIVA' => 'VA_BSI',
        'BSI_VA' => 'VA_BSI',
        'VA_BSI' => 'VA_BSI',
        'BRIVA' => 'VA_BRI',
        'BRI_VA' => 'VA_BRI',
        'VA_BRI' => 'VA_BRI',
        'BNIVA' => 'VA_BNI',
        'BNI_VA' => 'VA_BNI',
        'VA_BNI' => 'VA_BNI',
        'QRIS' => 'QRIS',
    ];

    public static function allowedAppMethods(): array
    {
        return array_keys(self::APP_TO_GATEWAY);
    }

    public static function normalizeAppMethod(?string $method): ?string
    {
        if ($method === null) {
            return null;
        }

        $normalized = strtoupper(str_replace(['-', ' '], '_', trim($method)));

        return self::ALIASES[$normalized] ?? null;
    }

    public static function gatewayMethodFor(string $appMethod): string
    {
        $normalized = self::normalizeAppMethod($appMethod);

        return $normalized ? self::APP_TO_GATEWAY[$normalized] : $appMethod;
    }

    public static function isSameMethod(?string $method, string $appMethod): bool
    {
        return self::normalizeAppMethod($method) === self::normalizeAppMethod($appMethod);
    }
}
