<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

class verifyToken
{
    public function handle(Request $request, Closure $next)
    {
        // try {
        //     $user = JWTAuth::parseToken()->authenticate();
        // } catch (TokenInvalidException $e) {
        //     return response()->json(['msg' => 'Token is invalid'], 401);
        // } catch (TokenExpiredException $e) {
        //     return response()->json(['msg' => 'Token has expired'], 401);
        // } catch (\Exception $e) {
        //     return response()->json(['msg' => 'Authorization Token not found'], 401);
        // }

        return $next($request);
    }
}
