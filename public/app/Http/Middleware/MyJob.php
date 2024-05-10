<?php

namespace App\Http\Middleware;

use App\Helpers\Validator;
use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use App\Models\JobList;
use App\Models\Professional;
use App\Models\Profile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MyJob extends Controller
{
    /**
     * This middleware filters request, the logged person may only have access to its own data (its professional profile curriculums and subsequents Objects)
     * The "handle" methods checks:
     * Fetches Professional object of logged person account & sets it to session
     * Fetches Curriculum object of logged person account & sets it to session if 'curriculum_id' is sent and route is not an exception (not to check route)
     * Note: This method has an array of exceptions which disable the required parameters such as 'curriculum_id' fetch and check!
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $person = auth('api')->user();
        $company = $person->getProfile(Profile::COMPANY);
        if(!$company)
            Validator::throwResponse('company not found', 401);
        $jobList = JobList::find(request('joblist_id'));
        if(!$jobList)
            Validator::throwResponse('job not found', 401);
        if($jobList->company_id != $company->company_id)
            Validator::throwResponse('not your job', 401);
        Session()->put('job', $jobList);
        return $next($request);
    }
}
