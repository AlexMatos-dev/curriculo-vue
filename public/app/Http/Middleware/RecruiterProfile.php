<?php

namespace App\Http\Middleware;

use App\Helpers\Validator;
use App\Models\Profile;
use App\Models\Recruiter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecruiterProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $person = auth('api')->user();
        $recruiter = $person->getProfile(Profile::RECRUITER);
        if(!$recruiter)
            Validator::throwResponse('recruiter profile not found', 403);
        Session()->put('person', $person);
        Session()->put('recruiter', $recruiter);
        return $next($request);
    }
}
