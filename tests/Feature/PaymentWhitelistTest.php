<?php

namespace Tests\Feature;

use Tests\TestCase;

class PaymentWhitelistTest extends TestCase
{
    /**
     * 测试：升级开启且白名单不包含时，禁止访问对应支付路由。
     *
     * Verify blocked provider returns 404 when upgrade whitelist is enforced.
     */
    public function testBlockedProviderReturns404()
    {
        // Enable upgrade flag and set whitelist to paypal,stripe
        // Request to alipay should be blocked and return 404
        config([
            'upgrade.enabled' => true,
            'upgrade.payment_whitelist' => ['paypal', 'stripe'],
        ]);

        $response = $this->get('/pay/alipay/alipayscan/ORDER123');
        $response->assertStatus(404);
    }
}