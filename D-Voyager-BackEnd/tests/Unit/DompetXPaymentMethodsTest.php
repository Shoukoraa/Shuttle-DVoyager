<?php

namespace Tests\Unit;

use App\Support\DompetXPaymentMethods;
use PHPUnit\Framework\TestCase;

class DompetXPaymentMethodsTest extends TestCase
{
    public function test_it_maps_app_methods_to_gateway_methods(): void
    {
        $this->assertSame('BRIVA', DompetXPaymentMethods::gatewayMethodFor('VA_BRI'));
        $this->assertSame('BNIVA', DompetXPaymentMethods::gatewayMethodFor('VA_BNI'));
        $this->assertSame('QRIS', DompetXPaymentMethods::gatewayMethodFor('QRIS'));
    }

    public function test_it_normalizes_gateway_methods_back_to_app_methods(): void
    {
        $this->assertSame('VA_BRI', DompetXPaymentMethods::normalizeAppMethod('BRIVA'));
        $this->assertSame('VA_BRI', DompetXPaymentMethods::normalizeAppMethod('bri-va'));
        $this->assertSame('VA_BCA', DompetXPaymentMethods::normalizeAppMethod('BCA_VA'));
        $this->assertSame('QRIS', DompetXPaymentMethods::normalizeAppMethod('qris'));
    }
}
