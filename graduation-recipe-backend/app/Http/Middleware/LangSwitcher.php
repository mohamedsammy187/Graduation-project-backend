<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LangSwitcher
{
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->header('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            app()->setLocale($lang);
        } else {
            app()->setLocale('en');
        }

        return $next($request);
    }
}
