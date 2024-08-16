<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\CommonCurrency;
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
use Stichoza\GoogleTranslate\GoogleTranslate;

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
     * @param String free_term
     * @param String location
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
        $translations = [
            'note: If your do not accept our cookies, you will not be able to login' => [
                'en' => 'note: If your do not accept our cookies, you will not be able to login',
                'pt' => 'obs: Se você não aceitar nossos cookies, você não poderá realizar o login',
                'es' => ''
            ],
            'know more at our' => [
                'en' => 'know more at our',
                'pt' => 'saiba mais em nossa',
                'es' => ''
            ],
            "by clicking 'Accept all cookies', you agree Jobifull can store cookies on your device and use information in accordance with our Cookie Policy" => [
                'en' => "by clicking 'Accept all cookies', you agree Jobifull can store cookies on your device and use information in accordance with our Cookie Policy",
                'pt' => "ao clicar em 'Aceitar todos os cookies', você concorda que a Jobifull pode armazenar cookies no seu dispositivo e utilizar informações de acordo com nossa Política de Cookies",
                'es' => "al hacer clic en 'Aceptar todas las cookies', aceptas que Jobifull pueda almacenar cookies en tu dispositivo y utilizar información de acuerdo con nuestra Política de Cookies"
            ],
            'cookies policy' => [
                'en' => 'cookies policy',
                'pt' => 'política de cookies',
                'es' => ''
            ],
            'you must accept our cookies to login' => [
                'en' => 'you must accept our cookies to login',
                'pt' => 'você deve aceitar os nossos cookies para realizar login',
                'es' => ''
            ],
            'do not accept' => [
                'en' => 'do not accept',
                'pt' => 'não aceito',
                'es' => ''
            ],
            'accept all' => [
                'en' => 'accept all',
                'pt' => 'aceito todos',
                'es' => ''
            ],
        ];

        $esGoogle = new GoogleTranslate('es', 'en');
        $path = storage_path('app/dbSourceFiles/systemTranslations.json');
        $data = json_decode(file_get_contents($path), true);
        echo count($data) . '<br><br>';
        $uno = (new ListLangue())->getNotOficialLangsIso();
        foreach($translations as $translation){
            $trans = $translation;
            $trans['es'] = mb_strtolower($esGoogle->translate($trans['en']));
            $unnoficial = [];
            foreach($uno as $langIso => $val){
                $unnoficial[$langIso] = null;
            }
            $trans['unoficialTranslations'] = $unnoficial;
            $data[] = $trans;
        }
        file_put_contents($path, json_encode($data));
        dd($data);
        

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
            'filter' => 'string',
            'free_term' => 'string',
            'location' => 'string'
        ]);
        $page       = (int)request('page', 1);
        $perPage    = (int)request('per_page', 10);
        if (!$perPage)
            $perPage = 10;
        $jobListObj = new JobList();
        if (request('filter'))
        {
            $results = [];
            switch (request('filter'))
            {
                case 'mostrecent':
                    $results = $jobListObj->getPaginatedJobs($this->request, $page, $perPage);
                    break;
                default:
                    $results = [
                        'results'  => [],
                        'page'     => 1,
                        'lastPage' => 1
                    ];
                    break;
            }
            returnResponse([
                'data' => $results['results'],
                'curent_page' => $results['page'],
                'per_page' => $perPage,
                'last_page' => $results['lastPage']
            ]);
        }
        else
        {
            $results = $jobListObj->getPaginatedJobs($this->request, $page, $perPage);
            returnResponse([
                'data' => $results['results'],
                'curent_page' => $results['page'],
                'per_page' => $perPage,
                'last_page' => $results['lastPage']
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
        $data = (new JobList())->getPaginatedJobs($this->request, 1, 1, 5, null, [$joblistId]);
        if (count($data['results']) < 1)
            Validator::throwResponse(translate('job not found'), 400);
        $job = $data['results'][0];
        if (!$job)
            Validator::throwResponse(translate('job not found'), 400);
        returnResponse(["message" => translate('job found successfully'), "data" => $job]);
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
     * @param String job_description - required
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
     * @param Int wage_currency - required
     * @param Float max_wage - required
     * @param Float minimun_wage - required
     * @param Int job_language - required
     * @return \Illuminate\Http\JsonResponse - Schema ["message" => String, "data" => Array]
     */
    public function store(Request $request)
    {
        $person = Auth::user();
        $company = $person->getProfile(Profile::COMPANY);
        if (!$company)
            Validator::throwResponse('company profile not found');
        if (!$company->isAdminOrRecruiter($person->person_id))
            Validator::throwResponse(translate('you do not have the rights, not your company'));
        try
        {
            Validator::validateParameters($request, [
                'job_modality_id'   => 'required|integer',
                'job_city'          => 'string|max:300',
                'job_state'         => 'string|max:300',
                'job_title'         => 'required|string|max:300',
                'job_country'       => 'required|integer',
                'job_seniority'     => 'integer',
                'minimum_wage'      => 'required|min:1',
                'max_wage'          => 'required|min:1',
                'job_description'   => 'max:5000',
                'experience_in_months' => 'integer',
                'job_experience_description' => 'max:5000',
                'job_benefits'      => 'max:5000',
                'job_offer'         => 'max:5000',
                'job_requirements'  => 'max:5000',
                'profession_for_job'=> 'integer',
                'payment_type'      => 'integer',
                'job_contract'      => 'integer',
                'working_visa'      => 'integer',
                'job_period'        => 'integer',
                'wage_currency'     => 'integer|required',
                'contact_email'     => 'string|max:300',
                'contact_name'      => 'string|max:300',
                'contact_phone'     => 'string|max:300',
                'contact_website'   => 'string|max:300',
                'job_language'      => 'integer|required'
            ]);
            $objects = Validator::checkExistanceOnTable([
                'job_modality_id'   => ['object' => JobModality::class,    'data' => $request->job_modality_id],
                'job_country'       => ['object' => ListCountry::class,    'data' => $request->job_country],
                'job_seniority'     => ['object' => Proficiency::class,    'data' => $request->job_seniority,      'required' => false],
                'profession_for_job' => ['object' => ListProfession::class, 'data' => $request->profession_for_job, 'required' => false],
                'payment_type'      => ['object' => JobPaymentType::class, 'data' => $request->payment_type,       'required' => false],
                'job_contract'      => ['object' => JobContract::class,    'data' => $request->job_contract,       'required' => false],
                'working_visa'      => ['object' => WorkingVisa::class,    'data' => $request->working_visa,       'required' => false],
                'job_period'        => ['object' => JobPeriod::class,      'data' => $request->job_period,         'required' => false],
                'wage_currency'     => ['object' => CommonCurrency::class, 'data' => $request->wage_currency],
                'job_language'      => ['object' => ListLangue::class,     'data' => $request->job_language],
            ]);
            if($request->job_seniority && $objects['job_seniority']->category != Proficiency::CATEGORY_SENIORITY)
                Validator::throwResponse(translate('invalid proficiency, must be seniority type'));
            $data = $request->all();
            $data['job_city'] = array_key_exists('job_city', $data) ? mb_strtolower($data['job_city']) : null;
            $data['job_state'] = array_key_exists('job_state', $data) ? mb_strtolower($data['job_state']) : null;
            $data['company_id'] = $company->company_id;
            $combos = ['profession_for_job', 'job_period', 'job_contract', 'working_visa', 'job_seniority', 'job_payment_type'];
            foreach ($combos as $key)
            {
                $data[$key] = request($key) == '' ? null : request($key);
            }
            $data['experience_in_months'] = (int)$data['experience_in_months'];
            $jobList = JobList::create($data);
            returnResponse(["message" => ucfirst(translate('job created successfully')), 'data' => $jobList->getJobFullData($jobList->job_id, [
                'company_id' => [$company->company_id],
                'status' => [$jobList::PUBLISHED_JOB, $jobList::PENDING_JOB, $jobList::DRAFT_JOB, $jobList::HIDDEN_JOB]
            ])]);
        }
        catch (ModelNotFoundException $e)
        {
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
        if (!$jobList)
            Validator::throwResponse(translate('job not found'));
        $person = Auth::user();
        $company = $person->getProfile(Profile::COMPANY);
        if (!$company)
            Validator::throwResponse('company profile not found');
        if (!$company->isAdminOrRecruiter($person->person_id) || $company->company_id != $jobList->company_id)
            Validator::throwResponse(translate('you do not have the rights, not your company'));
        try
        {
            Validator::validateParameters($request, [
                'job_modality_id'   => 'required|integer',
                'job_city'          => 'string|max:300',
                'job_state'         => 'string|max:300',
                'job_title'         => 'required|string|max:300',
                'job_country'       => 'required|integer',
                'job_seniority'     => 'integer',
                'minimum_wage'      => 'required|min:1',
                'max_wage'          => 'required|min:1',
                'job_description'   => 'max:3000',
                'experience_in_months' => 'integer',
                'job_experience_description' => 'max:3000',
                'job_benefits'      => 'max:3000',
                'job_offer'         => 'max:3000',
                'job_requirements'  => 'max:3000',
                'profession_for_job'=> 'integer',
                'payment_type'      => 'integer',
                'job_contract'      => 'integer',
                'working_visa'      => 'integer',
                'job_period'        => 'integer',
                'wage_currency'     => 'integer|required',
                'contact_email'     => 'string|max:300',
                'contact_name'      => 'string|max:300',
                'contact_phone'     => 'string|max:300',
                'contact_website'   => 'string|max:300',
                'job_language'      => 'integer|required'
            ]);
            $objects = Validator::checkExistanceOnTable([
                'job_modality_id'   => ['object' => JobModality::class,    'data' => $request->job_modality_id],
                'job_country'       => ['object' => ListCountry::class,    'data' => $request->job_country],
                'job_seniority'     => ['object' => Proficiency::class,    'data' => $request->job_seniority,      'required' => false],
                'profession_for_job' => ['object' => ListProfession::class, 'data' => $request->profession_for_job, 'required' => false],
                'payment_type'      => ['object' => JobPaymentType::class, 'data' => $request->payment_type,       'required' => false],
                'job_contract'      => ['object' => JobContract::class,    'data' => $request->job_contract,       'required' => false],
                'working_visa'      => ['object' => WorkingVisa::class,    'data' => $request->working_visa,       'required' => false],
                'job_period'        => ['object' => JobPeriod::class,      'data' => $request->job_period,         'required' => false],
                'wage_currency'     => ['object' => CommonCurrency::class, 'data' => $request->wage_currency,      'required' => false],
                'job_language'      => ['object' => ListLangue::class,     'data' => $request->job_language],
            ]);
            if ($request->job_seniority && $objects['job_seniority']->category != Proficiency::CATEGORY_SENIORITY)
                Validator::throwResponse(translate('invalid proficiency, must be seniority type'));
            $data = $request->all();
            $data['job_city'] = array_key_exists('job_city', $data) ? mb_strtolower($data['job_city']) : null;
            $data['job_state'] = array_key_exists('job_state', $data) ? mb_strtolower($data['job_state']) : null;
            $data['job_status'] = JobList::DRAFT_JOB;
            $combos = ['profession_for_job', 'job_period', 'job_contract', 'working_visa', 'job_seniority', 'job_payment_type'];
            foreach ($combos as $key)
            {
                $data[$key] = request($key) == '' ? null : request($key);
            }
            $data['experience_in_months'] = (int)$data['experience_in_months'];
            $result = $jobList->update($data);
            if (!$result)
                Validator::throwResponse(translate('job not updated'), 500);
            returnResponse(["message" => ucfirst(translate('job updated successfully')), 'data' => $jobList->getJobFullData(null, [
                'company_id' => [$company->company_id],
                'status' => [$jobList::PUBLISHED_JOB, $jobList::PENDING_JOB, $jobList::DRAFT_JOB, $jobList::HIDDEN_JOB]
            ])]);
        }
        catch (ModelNotFoundException $e)
        {
            returnResponse(["message" => ucfirst(translate('an error occurred while updating the job, please try again later')), "Error" => $e], 500);
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
     * Manages job languages, this method can ADD a new language, REMOVE, SYNC new languages (delete current and create anew) a current language or LIST all languages of job
     * @param Int joblist_id - required
     * @param String action - Either ('add', 'remove', 'sync' or 'list)
     * @param Int job_language_id
     * @param Int language_id
     * @param Int proficiency_id
     * @param Array job_languages_ids
     * @param Array job_languages_names
     * @param Array job_languages_seniorities
     * @return \Illuminate\Http\JsonResponse - Schema ["message" => String, "data" => Array]
     */
    public function manageJobLanguages()
    {
        Validator::validateParameters($this->request, [
            'action' => 'string|in:add,remove,list,sync',
            'language_id' => 'int',
            'proficiency_id' => 'int',
            'job_languages_ids' => 'array',
            'job_languages_names' => 'array',
            'job_languages_seniorities' => 'array'
        ]);
        $result = false;
        $data = null;
        switch (request('action'))
        {
            case 'add':
                $objects = Validator::checkExistanceOnTable([
                    'language' => ['object' => ListLangue::class, 'data' => request('language_id')],
                    'proficiency' => ['object' => Proficiency::class, 'data' => request('proficiency_id')],
                ]);
                if (request('language_id') && !request('proficiency_id'))
                    Validator::throwResponse(translate('no proficiency sent'));
                if ($objects['proficiency']->category != Proficiency::CATEGORY_LANGUAGE)
                    Validator::throwResponse(translate('invalid proficiency, category not accepted'));
                if (JobLanguage::create([
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
                $data = JobLanguage::where('joblist_id', $this->getJobBySession()->job_id)->leftJoin('jobslist', function ($join)
                {
                    $join->on('jobslist.job_id', '=', 'job_languages.joblist_id');
                })->leftJoin('listlangues', function ($join)
                {
                    $join->on('listlangues.llangue_id', '=', 'job_languages.language_id');
                })->leftJoin('proficiency', function ($join)
                {
                    $join->on('proficiency.proficiency_id', '=', 'job_languages.proficiency_id');
                })->get();
                break;
            case 'sync':
                $result = $this->getJobBySession()->syncLanguages(request('job_languages_ids'), request('job_languages_seniorities'));
                if ($result)
                {
                    $jobObj = $this->getJobBySession();
                    $data = $jobObj->getJobFullData(null, [
                        'company_id' => [$jobObj->company_id],
                        'status' => [$jobObj::PUBLISHED_JOB, $jobObj::PENDING_JOB, $jobObj::DRAFT_JOB, $jobObj::HIDDEN_JOB]
                    ]);
                }
                break;
        }
        if (!$result)
            Validator::throwResponse(translate('action not performed'), 500);
        returnResponse(['message' => translate('action performed'), 'data' => $data]);
    }

    /**
     * Manages job languages, SYNC new languages (delete current and create anew)
     * @param Int joblist_id - required
     * @param String action - Either ('sync')
     * @param Array driving_licenses_ids
     * @param Array driving_licenses_names
     * @return \Illuminate\Http\JsonResponse - Schema ["message" => String, "data" => Array]
     */
    public function manageDrivingLicenses()
    {
        Validator::validateParameters($this->request, [
            'action' => 'string|in:add,remove,list,sync',
            'driving_licenses_ids' => 'array',
            'driving_licenses_names' => 'array'
        ]);
        $result = false;
        $data = null;
        switch (request('action'))
        {
            case 'sync':
                $result = $this->getJobBySession()->syncDrivingLicenses(request('driving_licenses_ids'));
                if ($result)
                {
                    $jobObj = $this->getJobBySession();
                    $data = $jobObj->getJobFullData(null, [
                        'company_id' => [$jobObj->company_id],
                        'status' => [$jobObj::PUBLISHED_JOB, $jobObj::PENDING_JOB, $jobObj::DRAFT_JOB, $jobObj::HIDDEN_JOB]
                    ]);
                }
                break;
        }
        if (!$result)
            Validator::throwResponse(translate('action not performed'), 500);
        returnResponse(['message' => translate('action performed'), 'data' => $data]);
    }

    /**
     * Manages job visas, this method can ADD a new visa, REMOVE, SYNC (delete current and create anew) a current visa or LIST all visas of job
     * @param Int joblist_id - required
     * @param String action - Either ('add', 'remove', 'sync' or 'list')
     * @param Array job_visa_id
     * @param Array job_visas_ids
     * @param Array job_visas_names
     * @param Array visa_countries_ids
     * @return \Illuminate\Http\JsonResponse - Schema ["message" => String, "data" => Array]
     */
    public function manageJobVisas()
    {
        Validator::validateParameters($this->request, [
            'action' => 'string|in:add,remove,list,sync',
            'visas_type_id' => 'int',
            'country_id' => 'int',
            'job_visa_id' => 'int',
            'job_visas_ids' => 'array',
            'job_visas_names' => 'array',
            'visa_countries_ids' => 'array'
        ]);
        $result = false;
        $data = null;
        switch (request('action'))
        {
            case 'add':
                Validator::checkExistanceOnTable([
                    'visa' => ['object' => Visa::class, 'data' => request('visas_id')],
                    'visaType' => ['object' => TypeVisas::class, 'data' => request('visas_type_id')],
                    'country' => ['object' => ListCountry::class, 'data' => request('country_id')],
                ]);
                if (!JobVisa::where('joblist_id', $this->getJobBySession()->job_id)->where('visas_type_id', request('visas_type_id')->where('country_id', request('country_id')))->first())
                {
                    if (JobVisa::create([
                        'joblist_id' => $this->getJobBySession()->job_id,
                        'visas_type_id' => request('visas_type_id'),
                        'country_id' => request('country_id')
                    ]))
                        $result = true;
                }
                else
                {
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
                $data = JobVisa::where('joblist_id', $this->getJobBySession()->job_id)->leftJoin('jobslist', function ($join)
                {
                    $join->on('jobslist.job_id', '=', 'job_visas.joblist_id');
                })->leftJoin('listcountries', function ($join)
                {
                    $join->on('listcountries.lcountry_id', '=', 'job_visas.country_id');
                })->leftJoin('type_visas', function ($join)
                {
                    $join->on('type_visas.typevisas_id', '=', 'job_visas.visas_type_id');
                })->get();
                break;
            case 'sync':
                $result = $this->getJobBySession()->syncVisas(request('job_visas_ids'), request('visa_countries_ids'));
                if ($result)
                {
                    $jobObj = $this->getJobBySession();
                    $data = $jobObj->getJobFullData(null, [
                        'company_id' => [$jobObj->company_id],
                        'status' => [$jobObj::PUBLISHED_JOB, $jobObj::PENDING_JOB, $jobObj::DRAFT_JOB, $jobObj::HIDDEN_JOB]
                    ]);
                }
                break;
        }
        if (!$result)
            Validator::throwResponse(translate('action not performed'), 500);
        returnResponse(['message' => translate('action performed'), 'data' => $data]);
    }

    /**
     * Manages job skills, this method can ADD a new skill, REMOVE a current skill or LIST all skills of job
     * @param Int joblist_id - required
     * @param String action - Either ('add', 'remove', 'sync' or 'list)
     * @param Int tag_id
     * @param Int proficiency_id
     * @param Int job_skill_id
     * @param Array job_skills_ids
     * @param Array job_skills_names
     * @param Array job_skills_seniorities 
     * @return \Illuminate\Http\JsonResponse - Schema ["message" => String, "data" => Array]
     */
    public function manageJobSkills()
    {
        Validator::validateParameters($this->request, [
            'action' => 'string|in:add,remove,list,sync',
            'tag_id' => 'int',
            'proficiency_id' => 'int',
            'job_skill_id' => 'int',
            'job_skills_ids' => 'array',
            'job_skills_names' => 'array',
            'job_skills_seniorities' => 'array'
        ]);
        $result = false;
        $data = null;
        switch (request('action'))
        {
            case 'add':
                $data = Validator::checkExistanceOnTable([
                    'tag' => ['object' => Tag::class, 'data' => request('tag_id')],
                    'seniority' => ['object' => Proficiency::class, 'data' => request('proficiency_id')]
                ]);
                if ($data['seniority']->category != Proficiency::CATEGORY_LEVEL)
                    Validator::throwResponse(['message' => translate('invalid proficiency type'), 'data' => []]);
                if (!JobSkill::where('joblist_id', $this->getJobBySession()->job_id)->where('tag_id', request('tag_id'))->first())
                {
                    if (JobSkill::create([
                        'joblist_id' => $this->getJobBySession()->job_id,
                        'tag_id' => request('tag_id'),
                        'proficiency_id' => request('proficiency_id')
                    ]))
                        $result = true;
                }
                else
                {
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
                $data = JobSkill::where('joblist_id', $this->getJobBySession()->job_id)->leftJoin('jobslist', function ($join)
                {
                    $join->on('jobslist.job_id', '=', 'job_skills.joblist_id');
                })->leftJoin('tags', function ($join)
                {
                    $join->on('tags.tags_id', '=', 'job_skills.tag_id');
                })->get();
                break;
            case 'sync':
                $result = $this->getJobBySession()->syncSkills([], [
                    'job_skills_ids'   => request('job_skills_ids'),
                    'job_skills_names' => request('job_skills_names'),
                    'job_skills_seniorities' => request('job_skills_seniorities')
                ]);
                $jobObj = $this->getJobBySession();
                $data = $jobObj->getJobFullData(null, [
                    'company_id' => [$jobObj->company_id],
                    'status' => [$jobObj::PUBLISHED_JOB, $jobObj::PENDING_JOB, $jobObj::DRAFT_JOB, $jobObj::HIDDEN_JOB]
                ]);
                break;
        }
        if (!$result)
            Validator::throwResponse(translate('action not performed'), 500);
        returnResponse(['message' => translate('action performed'), 'data' => $data]);
    }
    
    /**
     * Manages certification, this method can SYNC certifications
     * @param Int joblist_id - required
     * @param String action - Either ('sync')
     * @param Array certifications_ids
     * @return \Illuminate\Http\JsonResponse - Schema ["message" => String, "data" => Array]
     */
    public function manageCertification()
    {
        Validator::validateParameters($this->request, [
            'action' => 'string|in:sync',
            'certifications_ids' => 'array',
            'certifications_names' => 'array',
            'countries_ids' => 'array'
        ]);
        $result = false;
        $data = null;
        switch(request('action')){
            case 'sync':
                $result = $this->getJobBySession()->syncCertifications(request('certifications_ids'), request('certifications_names'), request('countries_ids', []));
                $jobObj = $this->getJobBySession();
                $data = $jobObj->getJobFullData(null, [
                    'company_id' => [$jobObj->company_id], 
                    'status' => [$jobObj::PUBLISHED_JOB, $jobObj::PENDING_JOB, $jobObj::DRAFT_JOB, $jobObj::HIDDEN_JOB]
                ]);
            break;
        }
        if(!$result)
            Validator::throwResponse(translate('action not performed'), 500);
        returnResponse(['message' => translate('action performed'), 'data' => $data]);
    }

    /**
     * Get job contact information
     * @param Int jobId - required
     */
    public function getContactInformations($jobId)
    {
        try
        {
            $job = JobList::findOrFail($jobId);
            $result = [
                "contactEmail" => $job->contact_email,
                "contactName" => $job->contact_name,
                "contactPhone" => $job->contact_phone,
                "contactWebSite" => $job->contact_website,
            ];
            return response()->json($result);
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json(["message" => 'job not found', "error" => $e], 404);
        }
    }
}
