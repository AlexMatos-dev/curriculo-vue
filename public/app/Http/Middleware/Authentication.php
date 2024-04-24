<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->header('Authorization'))
            return response()->json(['message' => 'token not provided'], 401);
        if(!auth('api')->check())
            return response()->json(['message' => 'invalid token', 'generateToken' => true], 401);
        return $next($request);
    }
}
