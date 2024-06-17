<?php

namespace App\Http\Middleware;

use App\Helpers\Validator;
use App\Models\Profile;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $person = Auth::user();
        $professional = $person->getProfile(Profile::PROFESSIONAL);
        if(!$professional)
            Validator::throwResponse(translate('professional profile not found'), 403);
        Session()->put('person', $person);
        Session()->put('professional', $professional);
        return $next($request);
    }
}
