<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests;

class ApiThrottle extends ThrottleRequests
{
    protected $except = [
        'api/tags/search'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|null  $maxAttempts
     * @param  int|null  $decayMinutes
     * @param  string|null  $prefix
     * @return mixed
     */
    public function handle($request, \Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        if ($this->isException($request)) {
            return $next($request);
        }
        return parent::handle($request, $next, $maxAttempts, $decayMinutes, $prefix);
    }

    /**
     * Verifica se a rota está na lista de exceções
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function isException($request)
    {
        foreach ($this->except as $except) {
            if ($request->is($except)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns custom response if max request attempts performed
     */
    protected function buildResponse($key, $maxAttempts)
    {
        parent::buildResponse($key, $maxAttempts);
        returnResponse([
            'message' => ucfirst(translate('too many tries, wait for')) . ' ' . $this->limiter->availableIn($key) . ' ' . translate('before trying again'),
        ], 429);
    }

    protected function resolveRequestSignature($request)
    {
        return $request->fingerprint();
    }

    /**
     * Tries per minute
     */
    protected function resolveMaxAttempts($request, $maxAttempts)
    {
        return $maxAttempts ?? 6;
    }

    /**
     * Number of minute on the period
     */
    protected function resolveDecayMinutes($request, $decayMinutes)
    {
        return $decayMinutes ?? 1;
    }
}