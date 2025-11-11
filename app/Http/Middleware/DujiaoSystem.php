<?php

namespace App\Http\Middleware;

use App\Providers\AppServiceProvider;
use Closure;

class DujiaoSystem
{
    /**
     * 系统环境初始化（已移除 Dcat 相关配置）
     *
     * Purpose: previously forced HTTPS for Dcat Admin via config('admin').
     * With Filament migration complete, this middleware no longer alters
     * admin configuration. It simply passes the request through.
     */
    public function handle($request, Closure $next)
    {
        // No-op: Dcat Admin config removed. Keep middleware lightweight.
        return $next($request);
    }
}
