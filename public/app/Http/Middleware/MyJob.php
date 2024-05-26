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
     * This middleware filters request, the logged person may only have access to its own data (perform actions if his/her company is creator of job)
     * Note: There are exception to ownership check
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $jobList = JobList::find(request('joblist_id'));
        if(!$jobList)
            Validator::throwResponse('job not found', 403);
        if(!$this->isException()){
            $person = auth('api')->user();
            $company = $person->getProfile(Profile::COMPANY);
            if(!$company)
                Validator::throwResponse('company not found', 403);
            if($jobList->company_id != $company->company_id)
                Validator::throwResponse('not your job', 403);
        }
        Session()->put('job', $jobList);
        return $next($request);
    }

    /**
     * Checkes if sent parameter is of exception, in this case does no check ownership of job
     * @return Bool
     */
    public function isException()
    {
        $exceptionsParameters = [
            'action' => 'list'
        ];
        foreach($exceptionsParameters as $paramName => $paramValue){
            if($this->request->has($paramName) && request($paramName) == $paramValue)
                return true;
        }
        return false;
    }
}
