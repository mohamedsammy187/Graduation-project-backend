<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkpass
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->api_password != env("API_PASSWORD", "DLB6Ytpl6OyL3KTXOsgZk2mYbAlcJLtPF526dbJ4LuYU1W2t00yPx8FZmVmGWLgU")) {
            return response()->json(['msg' => 'UNAUTHORIZED']);
        }

        return $next($request);
    }
}
