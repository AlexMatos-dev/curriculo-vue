<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\JobApplied;
use App\Models\Plan;

class JobAppliedController extends Controller
{
    /**
     * Gets professional job application
     * @param Int job_id
     * @param Int company_id
     * @param String status
     * @return \Illuminate\Http\JsonResponse
     */
    public function professionalJobApplication()
    {
        $jobApplied = new JobApplied();
        $parameters = $this->request->all();
        $parameters['professional_id'] = $this->getProfessionalBySession()->professional_id;
        $data = $jobApplied->listJobApplied($parameters);
        return response()->json($data);
    }

    /**
     * Gets company job applications
     * @param Int job_id
     * @param String status
     * @return \Illuminate\Http\JsonResponse
     */
    public function companyJobApplication()
    {
        $jobApplied = new JobApplied();
        $parameters = $this->request->all();
        $parameters['company_id'] = $this->getObjectFromSession()->company_id;
        $data = $jobApplied->listJobApplied();
        return response()->json($data);
    }

    /**
     * Changes the job applied status
     * Note: only accesible by persons with a profile of recruiter or company and which owns the job professional applied
     * @param Int job_applied_id - required
     * @param String status - required
     */
    public function changeJobAppliedStatus()
    {
        $jobApplied = new JobApplied();
        Validator::validateParameters($this->request, [
            'job_applied_id' => 'required',
            'status' => 'required|in:'.implode(',', $jobApplied->getStatus())
        ]);
        $jobAppliedObj = $jobApplied->getJobAppliedByCompanyId($this->getObjectFromSession()->company_id);
        if(!$jobAppliedObj)
            Validator::throwResponse('job applied not found', 400);
        if($jobAppliedObj->status == request('status'))
            Validator::throwResponse('invalid status', 400);
        $jobAppliedObj->status = request('status');
        if(!$jobAppliedObj->save())
            Validator::throwResponse('status not changed', 500);
        $planObj = new Plan();
        if($this->getObjectFromSession()->paying && $planObj->canSendEmails($this->getObjectFromSession(), $this->getObjectType())){
            \App\Helpers\AsyncMethodHandler::sendEmailNotification(\App\Helpers\AsyncMethodHandler::NOTIFY_JOB_APPLIED_STATUS_CHANGE, [
                'status' => request('status'),
                'professional_id' => $jobAppliedObj->professional_id,
                'applied_id' => $jobAppliedObj->applied_id
            ]);
        }
        return response()->json(['data' => $jobAppliedObj], 200);
    }

    /**
     * List all possible JobApplied status
     * @return Array
     */
    public function listStatus()
    {
        $jobApplied = new JobApplied();
        return response()->json($jobApplied->getStatus());
    }
}
