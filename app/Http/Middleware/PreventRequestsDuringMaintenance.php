<?php

namespace App\Http\Middleware;

// 该中间件用于在维护模式期间阻止请求
// This middleware blocks requests during maintenance mode.
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [
        // Keep empty unless specific endpoints must bypass maintenance
    ];
}
