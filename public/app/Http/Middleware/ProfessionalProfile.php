<?php

namespace App\Http\Middleware;

use App\Helpers\Validator;
use App\Models\Profile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfessionalProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $person = auth('api')->user();
        if(!$person->getProfile(Profile::PROFESSIONAL))
            Validator::throwResponse('professional profile not found', 401);
        return $next($request);
    }
}
