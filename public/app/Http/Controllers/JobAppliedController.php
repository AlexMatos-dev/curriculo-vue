<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\JobApplied;
use App\Models\JobList;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;

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
            'status' => 'required|in:' . implode(',', $jobApplied->getStatus())
        ]);
        $jobAppliedObj = $jobApplied->getJobAppliedByCompanyId($this->getObjectFromSession()->company_id, request('job_applied_id'));
        if (!$jobAppliedObj)
            Validator::throwResponse('job applied not found', 400);
        if ($jobAppliedObj->status == request('status'))
            Validator::throwResponse('invalid status', 400);
        $jobAppliedObj->status = request('status');
        if (!$jobAppliedObj->save())
            Validator::throwResponse('status not changed', 500);
        $planObj = new Plan();
        if ($this->getObjectFromSession()->paying && $planObj->canSendEmails($this->getObjectFromSession(), $this->getObjectType()))
        {
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

    /**
     * Handle job application
     * @param Int job_id - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyForVacancy(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'job_id' => 'required'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Validator fields error',
                'errors' => $validator->errors()
            ], 400);
        }
        Validator::checkExistanceOnTable([
            'job_id' => ['object' => JobList::class, 'data' => request('job_id')]
        ]);
        if(JobApplied::where('professional_id', $this->getProfessionalBySession()->professional_id)->where('job_id', request('job_id'))->first())
            Validator::throwResponse('professional already applied for this job', 400);
        try
        {
            $jobApplied = JobApplied::create([
                'job_id' => $request->job_id,
                'professional_id' => $this->getProfessionalBySession()->professional_id,
                'status' => JobApplied::STATUS_APPLIED,
            ]);

            return response()->json([
                'data' => $jobApplied,
            ]);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create job application',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Removes a job applied
     * @param Int job_id - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelJobApplied()
    {
        Validator::validateParameters($this->request, [
            'job_id' => 'required'
        ]);
        $jobApplied = new JobApplied();
        $jobAppliedObj = $jobApplied->getJobAppliedByJobId(request('job_id'));
        if(!$jobAppliedObj || !$jobAppliedObj->isFromPorfessional($this->getProfessionalBySession()->professional_id))
            Validator::throwResponse('no job applied found', 400);
        if(!$jobApplied->delete())
            Validator::throwResponse('an error occured, job applied not removed', 500);
        Validator::throwResponse('job applied removed', 200);
    }
}
