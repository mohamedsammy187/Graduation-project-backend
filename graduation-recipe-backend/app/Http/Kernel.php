<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Middleware\Authenticate;

class Kernel extends HttpKernel
{
    // Global middleware
    protected $middleware = [
        // leave empty or add only necessary global middleware
    ];

    // Middleware groups
    protected $middlewareGroups = [
        'web' => [
            // add web-related middleware if needed
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            'bindings',
            \App\Http\Middleware\SetLanguage::class,
        ],
    ];

    protected $routeMiddleware = [
        'test' => \App\Http\Middleware\TestMiddleware::class,
        'CheckPass' => \App\Http\Middleware\CheckPass::class,
        'lang' => \App\Http\Middleware\LangSwitcher::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        // 'jwt.verify' => \PHPOpenSourceSaver\JWTAuth\Http\Middleware\Authenticate::class,

    ];
    //     protected $routeMiddleware = [
    //         'auth' => \App\Http\Middleware\Authenticate::class,
    //         'lang' => \App\Http\Middleware\LangMiddleware::class,
    //     ];
}
