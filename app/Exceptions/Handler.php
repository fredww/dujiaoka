<?php

namespace App\Exceptions;

// 该异常处理器负责报告与渲染异常，兼容 Laravel 10 的 Throwable 签名
// This handler reports and renders exceptions; updated to use Throwable for Laravel 10.
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * 报告或记录异常
     * Report or log an exception.
     */
    public function report(Throwable $e): void
    {
        // Delegate to base handler
        parent::report($e);
    }

    /**
     * 将异常渲染为 HTTP 响应
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Custom app exception rendering
        if ($e instanceof AppException) {
            $layout = dujiaoka_config_get('template', 'layui');
            $tplPath = $layout . '/errors/error';
            return view($tplPath, [
                'title' => __('dujiaoka.error_title'),
                'content' => $e->getMessage(),
                'url' => "",
            ]);
        }
        return parent::render($request, $e);
    }
}
