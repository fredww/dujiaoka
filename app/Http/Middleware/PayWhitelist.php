<?php

namespace App\Http\Middleware;

use Closure;

class PayWhitelist
{
    /**
     * 支付方式白名单中间件：在升级阶段仅允许白名单中的支付提供方访问。
     *
     * Purpose: Block non-whitelisted payment providers when upgrade flag is enabled.
     */
    public function handle($request, Closure $next)
    {
        // Read upgrade flags and allowed providers from config
        // Only enforce when upgrade.enabled === true
        if (config('upgrade.enabled')) {
            // Provider slug is the second segment under prefix 'pay' (e.g., /pay/stripe/...)
            // Extract provider from path to compare against whitelist
            $provider = strtolower((string) $request->segment(2));
            $allowed = (array) config('upgrade.payment_whitelist', []);

            // Abort if provider is not in whitelist
            if ($provider && !in_array($provider, $allowed, true)) {
                // Return 404 to avoid exposing deprecated payment endpoints
                return abort(404);
            }
        }

        return $next($request);
    }
}