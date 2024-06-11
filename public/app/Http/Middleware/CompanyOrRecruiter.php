<?php

namespace App\Http\Middleware;

use App\Models\Profile;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CompanyOrRecruiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $person = Auth::user();
        $company = $person->getProfile(Profile::COMPANY);
        $objectType = null;
        $currentObject = null;
        if($company){
            if(!$company)
                return response()->json(['message' => 'company or recruiter profile not found'], 403);
            $objectType = 'company';
            $currentObject = $company;
        }else{
            $person = Auth::user();
            $recruiter = $person->getProfile(Profile::RECRUITER);
            if(!$recruiter)
                return response()->json(['message' => 'company or recruiter profile not found'], 403);
            $objectType = 'recruiter';
            $currentObject = $recruiter;
        }
        Session()->put('objectType', $objectType);
        Session()->put($objectType, $currentObject);
        return $next($request);
    }
}
