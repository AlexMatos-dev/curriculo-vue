<?php

namespace App\Http\Middleware;

use App\Models\Profile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $person = auth('api')->user();
        $company = $person->getProfile(Profile::COMPANY);
        if(!$company)
            return response()->json(['message' => translate('company not found')], 403);
        if(!$company->isAdminWithPrivilegies($person->person_id))
            return response()->json(['message' => translate('you have no privilegies')], 403);
        Session()->put('company', $company);
        return $next($request);
    }
}
