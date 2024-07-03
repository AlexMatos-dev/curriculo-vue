<?php

namespace App\Http\Middleware;

use App\Helpers\Validator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AsynActions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$this->isOriginAllowed($_SERVER['HTTP_HOST']))
            Validator::throwResponse('not allowed', 500);
        return $next($request);
    }

    /**
     * Checks if origin can access this route
     */
    public function isOriginAllowed($origin = '')
    {
        $allowedOrigin = [
            str_replace(['http://', '/'], '', env('APP_URL'))
        ];
        return in_array($origin, $allowedOrigin);
    }
}
