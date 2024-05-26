<?php

namespace App\Http\Middleware;

use App\Helpers\Validator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AsynActions
{
    private $allowedOrigin = ['api.jobifull.eu'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!in_array($_SERVER['HTTP_HOST'], $this->allowedOrigin))
            Validator::throwResponse('not allowed', 500);
        return $next($request);
    }
}
