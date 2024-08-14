<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authentication
{
    private $request;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->request = $request;
        if($this->isException())
            return $next($request);
        if(!$request->header('Authorization'))
            returnResponse(['message' => translate('token not provided')], 401);
        if(!Auth::user())
            returnResponse(['message' => translate('invalid token'), 'generateToken' => true], 401);
        return $next($request);
    }

    /**
     * Validates wheter current request is an exception
     * @return Bool
     */
    public function isException()
    {
        $route = (object) [
            'uri' => $this->request->route()->uri(),
            'method' => $this->request->method()
        ];
        $exceptionRoutes = $this->getExceptionRoutes($route->uri);
        if(!$exceptionRoutes || !in_array($route->method, $exceptionRoutes['methods']))
            return false;
        return true;
    }

    /**
     * Returns the exception route array list or the expected key by parameter
     * @param String key
     * @return Array (List of routes or Route methods)
     */
    public function getExceptionRoutes($key = null)
    {
        $routesList = [
            'api/joblist' => ['methods' => ['GET']],
            'api/professional' => ['methods' => ['GET']],
        ];
        if($key)
            return array_key_exists($key, $routesList) ? $routesList[$key] : null;
        return $routesList;
    }
}
