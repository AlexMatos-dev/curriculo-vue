<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests;

class ApiThrottle extends ThrottleRequests
{
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