<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->header('Accept-Language', 'en');
        // app()->setlocale($lang == 'ar' ? 'ar' : 'en');

        if (str_starts_with($lang, 'ar')) {
            app()->setLocale('ar');
        } else {
            app()->setLocale('en');
        }
        return $next($request);
    }
}
