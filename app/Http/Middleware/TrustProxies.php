<?php

namespace App\Http\Middleware;

// 该中间件用于处理受信任代理头
// This middleware configures trusted proxy headers.
use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array|string|null
     */
    protected $proxies = null;

    /**
     * 代理检测的头部设置
     *
     * Purpose: set trusted proxy header bitmask compatible with Symfony 7 / Laravel 11.
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_PREFIX;
}
