<?php

namespace App\Http\Controllers;

use App\Helpers\ModelUtils;
use App\Helpers\Validator;
use App\Models\CommonCurrency;
use App\Models\Company;
use App\Models\CompanyType;
use App\Models\JobContract;
use App\Models\JobLanguage;
use App\Models\JobList;
use App\Models\JobModality;
use App\Models\JobPaymentType;
use App\Models\JobPeriod;
use App\Models\JobSkill;
use App\Models\JobVisa;
use App\Models\ListCountry;
use App\Models\ListLangue;
use App\Models\ListProfession;
use App\Models\Proficiency;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\TypeVisas;
use App\Models\Visa;
use App\Models\WorkingVisa;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JobListController extends Controller
{
    /**
     * Perform a search accordingly to sent parameters and returns a list ofjobs paginated and with a 'match' percentage
     * @param Int page - default 1
     * @param Array job_modality_id
     * @param Array company_id
     * @param Array job_city
     * @param Array job_state
     * @param Array job_country
     * @param Array job_seniority
     * @param Float job_salary_start
     * @param Float job_salary_end
     * @param String job_description
     * @param IntArray job_skills
     * @param String job_experience_description
     * @param Int experience_in_months_start
     * @param Int experience_in_months_end
     * @param Array job_visas
     * @param Array job_visas_countries
     * @param Int profession_for_job
     * @param Int payment_type
     * @param Int job_contract
     * @param Int working_visa
     * @param Int job_period
     * @param Int wage_currency
     * @param String filter - avaliable: (mostrecent)
     * @param Array job_driving_licenses
     * @param Array job_certifications
     * @param Int per_page
     * @return \Illuminate\Http\JsonResponse - Schema [
     *      "data": Array,
     *      "curent_page": int,
     *      "per_page": int,
     *      "last_page": int,
     * ]
     */
    public function index(Request $request)
    {
        set_time_limit(60);
        Validator::validateParameters($this->request, [
            'page' => 'integer',
            'company_id' => 'array',
            'job_modality_id' => 'array',
            'job_city' => 'array',
            'job_state' => 'array',
            'job_country' => 'array',
            'job_seniority' => 'array',
            'job_salary_start' => 'numeric',
            'job_salary_end' => 'numeric',
            'job_description' => 'max:500',
            'job_experience_description' => 'max:100',
            'experience_in_months_start' => 'integer',
            'experience_in_months_end' => 'integer',
            'job_visas' => 'array',
            'job_visas_countries' => 'array',
            'job_driving_licenses' => 'array',
            'job_certifications' => 'array',
            'per_page' => 'integer',
            'filter' => 'string'
        ]);
        $page = (int)request('page', 1);
        $perPage = (int)request('per_page', 100);
        if(!$perPage)
            $perPage = 10;
        $jobListObj = new JobList();
        $bdData = $jobListObj->getJobListBdData();
        if(request('filter')){
            $results = [];
            switch(request('filter')){
                case 'mostrecent':
                    $data = $jobListObj->listJobs($request, true);
                    $dataSize = count($data);
                    if($dataSize > 0){
                        if($dataSize < $perPage){
                            $notPaying = $jobListObj->listJobs($request, true);
                            $data = array_merge($data, $notPaying);
                        }
                        $results = $jobListObj->gatherJobJoinData($data, $bdData, $this->request);
                        $resized = [];
                        foreach($results as $result){
                            if(count($resized) < $perPage){
                                $resized[] = $result;
                            }else{
                                break;
                            }
                        }
                        $results = $resized;
                    }else{
                        $results = [];
                    }
                break;
            }
            returnResponse([
                'data' => $results,
                'curent_page' => 1,
                'per_page' => $perPage,
                'last_page' => 1
            ]);
        }else{
            $nonPaying = $jobListObj->listJobs($request, false);
            $nonPaying = $jobListObj->gatherJobJoinData($nonPaying, $bdData, $this->request);
            $paying = $jobListObj->listJobs($request, true);
            $paying = $jobListObj->gatherJobJoinData($paying, $bdData, $this->request);
            $orderedJobs = $jobListObj->orderByMatch($paying, $nonPaying);
            $results = $jobListObj->processListedJobs([
                'paying'    => $orderedJobs['paying'],
                'nonPaying' => $orderedJobs['nonPaying']
            ], $perPage, $page);
            if($page < 1){
                $page = 1;
            }else{
                $page = $page > $results['last_page'] ? $results['last_page'] : $page;
            }
            $data = [];
            if(!empty($results['results']) && !empty($results['results'][$page]))
                $data = $results['results'][$page];
            returnResponse([
                'data' => $data,
                'curent_page' => $page,
                'per_page' => $results['curent_page'],
                'last_page' => $results['last_page']
            ]);
        }
    }

    /**
     * Tries to get JobList from sent jobListId and them display its values
     * @param Int joblistId
     * @return \Illuminate\Http\JsonResponse - Schema ["message" => String, "data" => Array]
     */
    public function show(int $joblistId)
    {
        $obj = new JobList();
        $bdData = $obj->getJobListBdData();
        $jobList = $obj->getJob($joblistId);
        if(!$jobList)
            Validator::throwResponse(translate('job not found'), 400);
        $result = $jobList->listJobs($this->request, (bool)$jobList->paying, 1, null, true, [$jobList->job_id]);
        $jobListObj = $jobList->gatherJobJoinData($result, $bdData, $this->request)[0];
        returnResponse(["message" => translate('job found successfully'), "data" => $jobListObj]);
    }

    /**
     * Creates a new JobList by sent parameters
     * @param Int job_modality_id - required
     * @param String job_city
     * @param String job_state
     * @param String job_title
     * @param Int job_country
     * @param Int job_seniority
     * @param Float job_salary
     * @param String job_description
     * @param Int experience_in_months
     * @param String job_experience_description
     * @param String job_benefits
     * @param String job_offer
     * @param String job_requirements
     * @param Int profession_for_job
     * @param Int payment_type
     * @param Int job_contract
     * @param Int working_visa
     * @param Int job_period
     * @param Int wage_currency
     * @return \Illuminate\Http\JsonResponse - Schema ["message" => String, "data" => Array]
     */
    public function store(Request $request)
    {
        $person = Auth::user();
        $company = $person->getProfile(Profile::COMPANY);
        if(!$company)
            Validator::throwResponse('company profile not found');
        if(!$company->isAdminOrRecruiter($person->person_id))
            Validator::throwResponse(translate('you do not have the rights, not your company'));
        try{
            Validator::validateParameters($request, [
                'job_modality_id'   => 'required|integer',
                'job_city'          => 'string|max:300',
                'job_state'         => 'string|max:300',
                'job_title'         => 'required|string|max:300',
                'job_country'       => 'required|integer',
                'job_seniority'     => 'integer',
                'job_salary'        => 'required|min:0',
                'job_description'   => 'required|max:500',
                'experience_in_months' => 'integer',
                'job_experience_description' => 'max:500',
                'job_benefits'      => 'max:65535',
                'job_offer'         => 'max:65535',
                'job_requirements'  => 'max:65535',
                'profession_for_job'=> 'integer',
                'payment_type'      => 'integer',
                'job_contract'      => 'integer',
                'working_visa'      => 'integer',
                'job_period'        => 'integer',
                'wage_currency'     => 'integer'
            ]);
            $objects = Validator::checkExistanceOnTable([
                'job_modality_id'   => ['object' => JobModality::class,    'data' => $request->job_modality_id],
                'job_country'       => ['object' => ListCountry::class,    'data' => $request->job_country],
                'job_seniority'     => ['object' => Proficiency::class,    'data' => $request->job_seniority,      'required' => false],
                'profession_for_job'=> ['object' => ListProfession::class, 'data' => $request->profession_for_job, 'required' => false],
                'payment_type'      => ['object' => JobPaymentType::class, 'data' => $request->payment_type,       'required' => false],
                'job_contract'      => ['object' => JobContract::class,    'data' => $request->job_contract,       'required' => false],
                'working_visa'      => ['object' => WorkingVisa::class,    'data' => $request->working_visa,       'required' => false],
                'job_period'        => ['object' => JobPeriod::class,      'data' => $request->job_period,         'required' => false],
                'wage_currency'     => ['object' => CommonCurrency::class, 'data' => $request->wage_currency,      'required' => false],
            ]);
            if($request->job_state && !$request->job_country)
                Validator::throwResponse(translate('a country is required'));
            if($request->job_city && !$request->job_state)
                Validator::throwResponse(translate('a state is required'));
            if($request->job_seniority && $objects['job_seniority']->category != Proficiency::CATEGORY_SENIORITY)
                Validator::throwResponse(translate('invalid proficiency, must be seniority type'));
            $data = $request->all();
            $data['job_city'] = array_key_exists('job_city', $data) ? mb_strtolower($data['job_city']) : null;
            $data['job_state'] = array_key_exists('job_state', $data) ? mb_strtolower($data['job_state']) : null;
            $data['job_salary'] = (float)str_replace(',', '.', $request->job_salary);
            $data['company_id'] = $company->company_id;
            $jobList = JobList::create($data);
            returnResponse(["message" => translate('job created successfully'), 'data' => $jobList]);
        }
        catch (ModelNotFoundException $e){
            returnResponse(["message" => translate('an error occurred while creating the job, please try again later'), "error" => $e], 500);
        }
    }

    /**
     * Update JobList by sent parameters
     * @param Int job_modality_id - required
     * @param String job_city
     * @param String job_state
     * @param String job_title
     * @param Int job_country
     * @param Int job_seniority
     * @param Float job_salary
     * @param String job_description
     * @param Int experience_in_months
     * @param String job_experience_description
     * @param String job_benefits
     * @param String job_offer
     * @param String job_requirements
     * @param Int profession_for_job
     * @param Int payment_type
     * @param Int job_contract
     * @param Int working_visa
     * @param Int job_period
     * @param Int wage_currency
     * @return \Illuminate\Http\JsonResponse - Schema ["message" => String, "data" => Array]
     */
    public function update(int $jobListId, Request $request)
    {
        $jobList = JobList::find($jobListId);
        if(!$jobList)
            Validator::throwResponse(translate('job not found'));
        $person = Auth::user();
        $company = $person->getProfile(Profile::COMPANY);
        if(!$company)
            Validator::throwResponse('company profile not found');
        if(!$company->isAdminOrRecruiter($person->person_id) || $company->company_id != $jobList->company_id)
            Validator::throwResponse(translate('you do not have the rights, not your company'));
        try{
            Validator::validateParameters($request, [
                'job_modality_id'   => 'required|integer',
                'job_city'          => 'string|max:300',
                'job_state'         => 'string|max:300',
                'job_title'         => 'required|string|max:300',
                'job_country'       => 'required|integer',
                'job_seniority'     => 'integer',
                'job_salary'        => 'required|min:0',
                'job_description'   => 'required|max:500',
                'experience_in_months' => 'integer',
                'job_experience_description' => 'max:500',
                'job_benefits'      => 'max:65535',
                'job_offer'         => 'max:65535',
                'job_requirements'  => 'max:65535',
                'profession_for_job'=> 'integer',
                'payment_type'      => 'integer',
                'job_contract'      => 'integer',
                'working_visa'      => 'integer',
                'job_period'        => 'integer',
                'wage_currency'     => 'integer'
            ]);
            $objects = Validator::checkExistanceOnTable([
                'job_modality_id'   => ['object' => JobModality::class,    'data' => $request->job_modality_id],
                'job_country'       => ['object' => ListCountry::class,    'data' => $request->job_country],
                'job_seniority'     => ['object' => Proficiency::class,    'data' => $request->job_seniority,      'required' => false],
                'profession_for_job'=> ['object' => ListProfession::class, 'data' => $request->profession_for_job, 'required' => false],
                'payment_type'      => ['object' => JobPaymentType::class, 'data' => $request->payment_type,       'required' => false],
                'job_contract'      => ['object' => JobContract::class,    'data' => $request->job_contract,       'required' => false],
                'working_visa'      => ['object' => WorkingVisa::class,    'data' => $request->working_visa,       'required' => false],
                'job_period'        => ['object' => JobPeriod::class,      'data' => $request->job_period,         'required' => false],
                'wage_currency'     => ['object' => CommonCurrency::class, 'data' => $request->wage_currency,      'required' => false],
            ]);
            if($request->job_seniority && $objects['job_seniority']->category != Proficiency::CATEGORY_SENIORITY)
                Validator::throwResponse(translate('invalid proficiency, must be seniority type'));
            $data = $request->all();
            $data['job_city'] = array_key_exists('job_city', $data) ? mb_strtolower($data['job_city']) : null;
            $data['job_state'] = array_key_exists('job_state', $data) ? mb_strtolower($data['job_state']) : null;
            $data['job_salary'] = (float)str_replace(',', '.', $request->job_salary);
            $data['company_id'] = $company->company_id;
            $result = $jobList->update($data);
            if(!$result)
                Validator::throwResponse(translate('job not updated'), 500);
            returnResponse(["message" => translate('job updated successfully'), 'data' => $jobList]);
        }
        catch (ModelNotFoundException $e){
            returnResponse(["message" => translate('an error occurred while updating the job, please try again later'), "Error" => $e], 500);
        }
    }

    /**
     * Removes a JobList and all related information
     * @param Int jobListId - required
     * @return \Illuminate\Http\JsonResponse - Schema ["message" => String, "data" => Array] 
     */
    public function destroy(int $jobListId)
    {
        try
        {
            $jobList = JobList::findOrFail($jobListId);
            JobVisa::where('joblist_id', $jobList->job_id);
            JobVisa::where('joblist_id', $jobList->job_id);
            JobSkill::where('joblist_id', $jobList->job_id);
            JobModality::where('joblist_id', $jobList->job_id);
            JobLanguage::where('joblist_id', $jobList->job_id);
            $jobList->delete();
            returnResponse(["message" => translate('job deleted sucessfully')]);
        }
        catch (ModelNotFoundException $e)
        {
            returnResponse(["message" => translate('job not found'), "error" => $e], 404);
        }
    }

    /**
     * Manages job languages, this method can ADD a new language, REMOVE a current language or LIST all languages of job
     * @param Int joblist_id - required
     * @param Stirng action - Either ('add', 'remove' or 'list)
     * @param Int job_language_id
     * @param Int language_id
     * @param Int proficiency_id
     * @return \Illuminate\Http\JsonResponse - Schema ["message" => String, "data" => Array]
     */
    public function manageJobLanguages()
    {
        Validator::validateParameters($this->request, [
            'action' => 'string|in:add,remove,list',
            'language_id' => 'int',
            'proficiency_id' => 'int',
        ]);
        $result = false;
        $data = null;
        switch(request('action')){
            case 'add':
                $objects = Validator::checkExistanceOnTable([
                    'language' => ['object' => ListLangue::class, 'data' => request('language_id')],
                    'proficiency' => ['object' => Proficiency::class, 'data' => request('proficiency_id')],
                ]);
                if(request('language_id') && !request('proficiency_id'))
                    Validator::throwResponse(translate('no proficiency sent'));
                if($objects['proficiency']->category != Proficiency::CATEGORY_LANGUAGE)
                    Validator::throwResponse(translate('invalid proficiency, category not accepted'));
                if(JobLanguage::create([
                    'joblist_id' => $this->getJobBySession()->job_id,
                    'language_id' => request('language_id'),
                    'proficiency_id' => request('proficiency_id')
                ]))
                    $result = true; 
            break;
            case 'remove':
                $object = Validator::checkExistanceOnTable([
                    'jobLanguage' => ['object' => JobLanguage::class, 'data' => request('job_language_id')],
                ]);
                $result = $object['jobLanguage']->delete();
            break;
            case 'list':
                $result = true;
                $data = JobLanguage::where('joblist_id', $this->getJobBySession()->job_id)->leftJoin('jobslist', function($join){
                    $join->on('jobslist.job_id', '=', 'job_languages.joblist_id');
                })->leftJoin('listlangues', function($join){
                    $join->on('listlangues.llangue_id', '=', 'job_languages.language_id');
                })->leftJoin('proficiency', function($join){
                    $join->on('proficiency.proficiency_id', '=', 'job_languages.proficiency_id');
                })->get();
            break;
        }
        if(!$result)
            Validator::throwResponse(translate('action not performed'), 500);
        returnResponse(['message' => translate('action performed'), 'data' => $data]);
    }

    /**
     * Manages job visas, this method can ADD a new visa, REMOVE a current visa or LIST all visas of job
     * @param Int joblist_id - required
     * @param Stirng action - Either ('add', 'remove' or 'list)
     * @param Int visas_type_id
     * @param Int country_id
     * @param Int job_visa_id
     * @return \Illuminate\Http\JsonResponse - Schema ["message" => String, "data" => Array]
     */
    public function manageJobVisas()
    {
        Validator::validateParameters($this->request, [
            'action' => 'string|in:add,remove,list',
            'visas_type_id' => 'int',
            'country_id' => 'int',
            'job_visa_id' => 'int'
        ]);
        $result = false;
        $data = null;
        switch(request('action')){
            case 'add':
                Validator::checkExistanceOnTable([
                    'visa' => ['object' => Visa::class, 'data' => request('visas_id')],
                    'visaType' => ['object' => TypeVisas::class, 'data' => request('visas_type_id')],
                    'country' => ['object' => ListCountry::class, 'data' => request('country_id')],
                ]);
                if(!JobVisa::where('joblist_id', $this->getJobBySession()->job_id)->where('visas_type_id', request('visas_type_id')->where('country_id', request('country_id')))->first()){
                    if(JobVisa::create([
                        'joblist_id' => $this->getJobBySession()->job_id,
                        'visas_type_id' => request('visas_type_id'),
                        'country_id' => request('country_id')
                    ]))
                        $result = true; 
                }else{
                    Validator::throwResponse(['message' => translate('visa already added to job'), 'data' => $data]);
                }
            break;
            case 'remove':
                $object = Validator::checkExistanceOnTable([
                    'jobVisa' => ['object' => JobVisa::class, 'data' => request('job_visa_id')],
                ]);
                $result = $object['jobVisa']->delete();
            break;
            case 'list':
                $result = true;
                $data = JobVisa::where('joblist_id', $this->getJobBySession()->job_id)->leftJoin('jobslist', function($join){
                    $join->on('jobslist.job_id', '=', 'job_visas.joblist_id');
                })->leftJoin('listcountries', function($join){
                    $join->on('listcountries.lcountry_id', '=', 'job_visas.country_id');
                })->leftJoin('type_visas', function($join){
                    $join->on('type_visas.typevisas_id', '=', 'job_visas.visas_type_id');
                })->get();
            break;
        }
        if(!$result)
            Validator::throwResponse(translate('action not performed'), 500);
        returnResponse(['message' => translate('action performed'), 'data' => $data]);
    }

    /**
     * Manages job skills, this method can ADD a new skill, REMOVE a current skill or LIST all skills of job
     * @param Int joblist_id - required
     * @param Stirng action - Either ('add', 'remove' or 'list)
     * @param Int tag_id
     * @param Int job_skill_id
     * @return \Illuminate\Http\JsonResponse - Schema ["message" => String, "data" => Array]
     */
    public function manageJobSkills()
    {
        Validator::validateParameters($this->request, [
            'action' => 'string|in:add,remove,list',
            'tag_id' => 'int',
            'job_skill_id' => 'int'
        ]);
        $result = false;
        $data = null;
        switch(request('action')){
            case 'add':
                Validator::checkExistanceOnTable([
                    'tag' => ['object' => Tag::class, 'data' => request('tag_id')]
                ]);
                if(!JobSkill::where('joblist_id', $this->getJobBySession()->job_id)->where('tag_id', request('tag_id'))->first()){
                    if(JobSkill::create([
                        'joblist_id' => $this->getJobBySession()->job_id,
                        'tag_id' => request('tag_id')
                    ]))
                        $result = true;
                }else{
                    Validator::throwResponse(['message' => translate('skill already added to job'), 'data' => $data]);
                } 
            break;
            case 'remove':
                $object = Validator::checkExistanceOnTable([
                    'jobSkill' => ['object' => JobSkill::class, 'data' => request('job_skill_id')],
                ]);
                $result = $object['jobSkill']->delete();
            break;
            case 'list':
                $result = true;
                $data = JobSkill::where('joblist_id', $this->getJobBySession()->job_id)->leftJoin('jobslist', function($join){
                    $join->on('jobslist.job_id', '=', 'job_skills.joblist_id');
                })->leftJoin('tags', function($join){
                    $join->on('tags.tags_id', '=', 'job_skills.tag_id');
                })->get();
            break;
        }
        if(!$result)
            Validator::throwResponse(translate('action not performed'), 500);
        returnResponse(['message' => translate('action performed'), 'data' => $data]);
    }
}
