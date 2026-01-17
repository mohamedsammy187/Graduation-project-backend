<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \Illuminate\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    protected $middlewareGroups = [

        'web' => [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $routeMiddleware = [
        'auth'     => \App\Http\Middleware\Authenticate::class,
        'lang'     => \App\Http\Middleware\SetLanguage::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'is_admin' => \App\Http\Middleware\IsAdmin::class,
        'test'     => \App\Http\Middleware\TestingMiddleware::class,

        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ];
}
