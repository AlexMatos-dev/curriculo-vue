<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\Company;
use App\Models\CompanyType;
use App\Models\JobList;
use App\Models\Person;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Perform a search accordingly to sent parameters and returns a list ofjobs paginated and with a 'match' percentage
     * @param Int page - default 1
     * @param String company_register_number
     * @param String company_name
     * @param Int company_type 
     * @param String company_description
     * @param Int start_company_number_employees
     * @param Int end_company_number_employees
     * @param String company_benefits
     * @param Int per_page
     * @return \Illuminate\Http\JsonResponse - Schema [
     *      "data": Array,
     *      "curent_page": int,
     *      "per_page": int,
     *      "last_page": int,
     * ]
     */
    public function index()
    {
        Validator::validateParameters($this->request, [
            'page' => 'integer',
            'company_register_number' => 'string',
            'company_name' => 'string',
            'company_type' => 'numeric',
            'company_description' => 'string',
            'start_company_number_employees' => 'numeric',
            'end_company_number_employees' => 'numeric',
            'company_benefits' => 'string',
            'per_page' => 'integer'
        ]);
        $page       = (int)request('page', 1);
        $perPage    = (int)request('per_page', 10);
        if(!$perPage)
            $perPage = 10;
        $companyListObj = new Company();
        $results = $companyListObj->getPaginatedCompanies($this->request, $page, $perPage);
        returnResponse([
            'data' => $results['results'],
            'curent_page' => $results['page'],
            'per_page' => $perPage,
            'last_page' => $results['lastPage']
        ]);
    }

    /**
     * Tries to get Company from sent companySlug and them display its values
     * @param String companySlug
     * @return \Illuminate\Http\JsonResponse - Schema ["message" => String, "data" => Array]
     */
    public function show(String $companySlug)
    {
        $company = Company::where('company_slug', $companySlug)->first();
        if(!$company || (is_array($company) && !empty($company)))
            Validator::throwResponse(translate('company not found'), 400);
        $result = $company->listCompanies($this->request, (bool)$company->paying, 1, null, [$company->company_id]);
        if(empty($result))
            Validator::throwResponse(translate('company not found'), 400);
        $company = $company->gatherCompanyInfo($result);
        if(empty($company))
            Validator::throwResponse(translate('company not found'), 400);
        returnResponse(["message" => translate('company found'), "data" => $company[0]]);
    }

    /**
     * Updates logged person Company account.
     * @param String company_register_number - required
     * @param String company_name - required
     * @param Int    company_type - required
     * @param String company_video
     * @param String company_email
     * @param String company_phone
     * @param String company_website
     * @param String company_description
     * @param String company_number_employees
     * @param String company_benefits
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        Validator::validateParameters($this->request, [
            'company_register_number' => 'required|max:100',
            'company_name' => 'required|max:300',
            'company_type' => 'required|integer',
            'company_video' => 'max:150',
            'company_email' => 'required|email|max:150',
            'company_phone' => 'max:20',
            'company_website' => 'max:100',
            'company_description' => 'max:500',
            'company_number_employees' => 'numeric|not_in:0',
            'company_benefits' => 'max:4294967295'
        ]);
        Validator::checkExistanceOnTable([
            'company_type' => ['object' => CompanyType::class, 'data' => request('company_type')]
        ]);
        $person = Auth::user();
        $dataToSet = [
            'company_register_number' => request('company_register_number'),
            'company_slug' => $person->makeSlug(request('company_name'), null),
            'company_name' => request('company_name'),
            'company_type' => request('company_type'),
            'company_video' => request('company_video'),
            'company_email' => request('company_email'),
            'company_phone' => request('company_phone'),
            'company_website' => request('company_website'),
            'company_description' => request('company_description'),
            'company_number_employees' => request('company_number_employees'),
            'company_benefits' => request('company_benefits'),
            'person_id' => $person->person_id
        ];
        if(request('company_logo')){
            $imageHandler = Validator::validateImage(request('company_logo'));
            $dataToSet['company_logo'] = base64_encode($imageHandler->generateImageThumbanil());
            $imageHandler->destroyFile();
        }
        if(request('company_cover_photo')){
            $imageHandler = Validator::validateImage(request('company_cover_photo'));
            $dataToSet['company_cover_photo'] = base64_encode($imageHandler->generateImageThumbanil());
            $imageHandler->destroyFile();
        }
        $company = $person->getProfile(Profile::COMPANY);
        if(!$company || ($company && $company->company_email != request('company_email')))
            Validator::validateParameters($this->request, ['company_email' => 'unique:companies']);
        $newCompany = false;
        if(!$company){
            $response = (new Company())->saveCompany($dataToSet);
            if(!$response)
                returnResponse(['message' => translate('professional not found')], 500);
            $company = $response;
            $newCompany = true;
        }else{
            if(!$company->isAdmin($person->person_id))
                returnResponse(['message' => translate('not an admin of company')], 401);
            if(!$company->saveCompany($dataToSet))
                returnResponse(['message' => translate('company not updated')], 500);
        }
        if($newCompany){
            $result = Profile::create([
                'person_id' => $person->person_id,
                'profile_type_id' => $company->company_id,
                'profile_type' => Profile::COMPANY
            ]);
            if(!$result){
                $company->delete();
                returnResponse(['message' => translate('company not updated')], 500);
            }
            $company->syncAdmins($person->person_id, true);
        }
        returnResponse($company);
    }

    /**
     * Manages company admins, this method can ADD a new admin, REMOVE a current admin, GRANT privilegies and REVOKE privilegies of an admin
     * Obs: Only admins with privilegies can perform an action!
     * @param String action - possible values ['add', 'remove', 'grant', 'revoke']
     * @param Int person_id - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function manageCompanyAdmin()
    {
        $actions = ['add', 'remove', 'grant', 'revoke', 'list'];
        if(!in_array(request('action'), $actions))
            returnResponse(['message' => translate('invalid action')], 400);
        $person = Auth::user();
        $company = Session()->get('company') ? $this->getCompanyBySession() : $person->getProfile(Profile::COMPANY);
        $targetPerson = Person::find(request('person_id'));
        if(request('action') != 'list' && (!$targetPerson || $person->person_id == $targetPerson->person_id))
            returnResponse(['message' => translate('person is invalid')], 400);
        $data = [];
        switch(request('action')){
            case 'add':
                $result = $company->syncAdmins(request('person_id'));
            break;
            case 'remove':
                $result = $company->removeAdmin(request('person_id'));
            break;
            case 'grant':
                $result = $company->hadleAdminPivilegies(request('person_id'), true);
            break;
            case 'revoke':
                $result = $company->hadleAdminPivilegies(request('person_id'), false);
            break;
            case 'list':
                $data = $company->getAdmins();
                $result = true;
            break;
        }
        if(!$result)
            returnResponse(['message' => translate('action not completed with success')], 500);
        returnResponse(['message' => translate('action performed'), 'data' => $data]);
    }

    /**
     * Manages company recruiters, this method can ADD a new recruiter, REMOVE a current recruit or LIST all recruiters of company
     * Obs: Only admins with privilegies can perform an action!
     * @param String action - possible values ['add', 'remove or 'list]
     * @param Int person_id - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function manageCompanyRecruiter()
    {
        $actions = ['add', 'remove', 'list'];
        if(!in_array(request('action'), $actions))
            returnResponse(['message' => translate('invalid action')], 400);
        $person = Auth::user();
        $company = Session()->get('company') ? $this->getCompanyBySession() : $person->getProfile(Profile::COMPANY);
        $targetPerson = Person::find(request('person_id'));
        if(request('action') != 'list' && (!$targetPerson || $person->person_id == $targetPerson->person_id))
            returnResponse(['message' => translate('person is invalid')], 400);
        switch(request('action')){
            case 'add':
                $result = $company->addRecruiter($targetPerson->person_id);
            break;
            case 'remove':
                $result = $company->removeRecruiter($targetPerson->person_id);
            break;
            case 'list':
                $result = true;
            break;
        }
        if(!$result)
            returnResponse(['message' => translate('action not completed with success')], 500);
        returnResponse(['message' => translate('action performed'), 'data' => $company->getRecruiters()]);
    }

    /**
     * Gets all my company jobs
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyCompanyJobs()
    {
        $jobsIds = request('job_id') ? [request('job_id')] : [];
        $page    = (int)request('page', 1);
        $perPage = (int)request('per_page', 10);
        if(!$perPage)
            $perPage = 10;
        $jobListObj = new JobList();
        $company = $this->getCompanyBySession();
        $results = $jobListObj->getPaginatedJobs($this->request, $page, $perPage, 5, [
            'company_id' => [$company->company_id], 
            'status' => [$jobListObj::PUBLISHED_JOB, $jobListObj::PENDING_JOB, $jobListObj::DRAFT_JOB, $jobListObj::HIDDEN_JOB]
        ], $jobsIds);
        $results['results'] = $jobListObj->setApplicationsToJobs($results['results']);
        returnResponse([
            'data' => $results['results'],
            'curent_page' => $results['page'],
            'per_page' => $perPage,
            'last_page' => $results['lastPage']
        ]);
    }

    /**
     * Gets company job details
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchCompanyJob()
    {
        $jobsIds = request('job_id') ? [request('job_id')] : [];
        $page    = (int)request('page', 1);
        $perPage = (int)request('per_page', 1);
        $jobListObj = new JobList();
        $company = $this->getCompanyBySession();
        $results = $jobListObj->getPaginatedJobs($this->request, $page, $perPage, 5, [
            'company_id' => [$company->company_id], 
            'status' => [$jobListObj::PUBLISHED_JOB, $jobListObj::PENDING_JOB, $jobListObj::DRAFT_JOB, $jobListObj::HIDDEN_JOB]
        ], $jobsIds);
        if(count($results['results']) != 1)
            Validator::throwResponse(translate('job not found'), 400);
        returnResponse(["message" => translate('job found successfully'), "data" => $results['results'][0]]);
    }

    /**
     * Sends a draft job to validation
     * @param Int job_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postJob()
    {
        $job = JobList::find(request('job_id'));
        if(!$job)
            Validator::throwResponse(translate('job not found'), 400);
        if(in_array($job->job_status, [JobList::PENDING_JOB, JobList::PUBLISHED_JOB]))
            returnResponse(['message' => translate('job already published or in validation'), 'data' => $job], 200);
        // Here there will be a method to check it and let it on validation
        $job->job_status = JobList::PUBLISHED_JOB;
        if(!$job->save())
            Validator::throwResponse(translate('job not published, try again later'), 500);
        returnResponse(['message' => translate('job published'), 'data' => $job->getJobFullData(null, [
            'company_id' => [$job->company_id], 
            'status' => [$job::PUBLISHED_JOB, $job::PENDING_JOB, $job::DRAFT_JOB, $job::HIDDEN_JOB]
        ])], 200);
    }

    /**
     * Sends a job to trash (inactivate it)
     * @param Int job_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function desactivateJob()
    {
        $job = JobList::find(request('job_id'));
        if(!$job)
            Validator::throwResponse(translate('job not found'), 400);
        $job->job_status = JobList::HIDDEN_JOB;
        if(!$job->save())
            Validator::throwResponse(translate('job not sent to trash, try again later'), 500);
        returnResponse(['message' => translate('job sent to trash'), 'data' => $job->getJobFullData(null, [
            'company_id' => [$job->company_id], 
            'status' => [$job::PUBLISHED_JOB, $job::PENDING_JOB, $job::DRAFT_JOB, $job::HIDDEN_JOB]
        ])], 200);
    }

    /**
     * Sends a job to trash (inactivate it)
     * @param Int job_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reactivateJob()
    {
        $job = JobList::find(request('job_id'));
        if(!$job)
            Validator::throwResponse(translate('job not found'), 400);
        $job->job_status = JobList::DRAFT_JOB;
        if(!$job->save())
            Validator::throwResponse(translate('job not sent to trash, try again later'), 500);
        returnResponse(['message' => translate('job removed from trash'), 'data' => $job->getJobFullData(null, [
            'company_id' => [$job->company_id], 
            'status' => [$job::PUBLISHED_JOB, $job::PENDING_JOB, $job::DRAFT_JOB, $job::HIDDEN_JOB]
        ])], 200);
    }
}
