<?php

namespace App\Models;

use App\Helpers\ModelUtils;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JobList extends Model
{
    const PUBLISHED_JOB = 'published';
    const PENDING_JOB   = 'validating';
    const DRAFT_JOB     = 'draft';
    const HIDDEN_JOB    = 'hidden';

    protected $primaryKey = 'job_id';
    protected $table = 'jobslist';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'job_modality_id',
        'job_title',
        'job_city',
        'job_state',
        'job_country',
        'job_seniority',
        'minimum_wage',
        'max_wage',
        'job_description',
        'job_experience_description',
        'experience_in_months',
        'job_benefits',
        'job_offer',
        'job_requirements',
        'profession_for_job',
        'payment_type',
        'job_contract',
        'working_visa',
        'job_period',
        'wage_currency',
        'job_language',
        'job_status',
        'contact_email',
        'contact_name',
        'contact_phone'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function modality()
    {
        return $this->belongsTo(JobModality::class, 'job_modality_id')->first();
    }

    public function jobSeniority()
    {
        return $this->belongsTo(JobModality::class, 'job_modality_id')->first();
    }

    public function jobPeriod()
    {
        return $this->belongsTo(JobPeriod::class, 'job_period')->first();
    }

    public function paymentType()
    {
        return $this->belongsTo(JobPaymentType::class, 'job_payment_type')->first();
    }

    public function jobContract()
    {
        return $this->belongsTo(JobContract::class, 'job_contract')->first();
    }

    public function workingVisa()
    {
        return $this->belongsTo(workingVisa::class, 'working_visa')->first();
    }

    public function jobEnglishLevel()
    {
        return $this->belongsTo(JobModality::class, 'job_modality_id')->first();
    }

    public function jobSkills()
    {
        return JobSkill::where('joblist_id', $this->job_id)->get();
    }

    /**
     * Lists jobs, filtering rsults accordingly to sent parameters
     * @param Illuminate\Http\Request $request - schema: [ 'job_modality_id' => int, 'job_modality_id' => int, 'job_country' => int
     *      'job_city' => string, 'job_seniority' => int, 'job_salary_start' => float, 'job_salary_end' => float, 'job_state' => Array
     *      'experience_in_months_start' => integer, 'experience_in_months_end' => integer, 'job_skills' => IntArray,
     *      'profession_for_job' => integer, 'payment_type' => integer, 'job_contract' => integer,
     *      'working_visa' => integer, 'job_period' => integer, 'wage_currency' => integer, 'job_driving_licenses' => IntArray, 
     *      'job_certifications' => IntArray
     * ]
     * @param Bool paying
     * @param Int limit
     * @param Int offset
     * @return Array
     */
    public function listJobs(\Illuminate\Http\Request $request, $paying = false, $limit = 200, $offset = null, $distinct = true, $byIds = [])
    {
        $limit = !is_numeric($limit) || $limit > 200 ? 200 : $limit;
        if($byIds)
            $limit = count($byIds);
        if($distinct){
            $query = JobList::select(
                'jobslist.job_id', 'jobslist.created_at', 'jobslist.job_status', 'jobslist.*', 'company.company_logo', 'company.company_cover_photo', 'company.company_description', 
                'company.paying', 'jobslist.created_at AS job_created_at', 'jobslist.updated_at AS job_updated_at', 'company.company_type',
                'company.company_name'
            )->distinct();
        }else{
            $query = JobList::select(
                'jobslist.*', 'company.*', 'jobslist.created_at AS job_created_at', 'jobslist.updated_at AS job_updated_at'
            );
        }
        $query->leftJoin('companies AS company', function($join){
            $join->on('jobslist.company_id', '=', 'company.company_id');
        })->leftJoin('job_skills AS skills', function($join){
            $join->on('jobslist.job_id', '=', 'skills.joblist_id');
        })->leftJoin('job_languages AS languages', function($join){
            $join->on('jobslist.job_id', '=', 'languages.joblist_id');
        })->leftJoin('job_visas AS job_visas', function($join){
            $join->on('jobslist.job_id', '=', 'job_visas.joblist_id');
        })->leftJoin('job_driving_licenses', function($join){
            $join->on('jobslist.job_id', '=', 'job_driving_licenses.job_id');
        })->leftJoin('job_certifications', function($join){
            $join->on('jobslist.job_id', '=', 'job_certifications.joblist_id');
        })->where('company.paying', $paying)->where('job_status', $this::PUBLISHED_JOB);
        if($byIds && !empty($byIds))
            $query->whereIn('jobslist.job_id', $byIds);
        if ($request->has("company_id")) 
            $query->whereIn("jobslist.company_id", $request->company_id);
        if ($request->has("job_modality_id")) 
            $query->whereIn("jobslist.job_modality_id", $request->job_modality_id);
        if ($request->has("job_country"))
            $query->whereIn("jobslist.job_country", $request->job_country);
        if ($request->has("job_city")){
            foreach($request->job_city as $cityName){
                $query->orWhere("jobslist.job_city", 'like', '%'.$cityName.'%');
            }
        }
        if ($request->has("job_state")){
            foreach($request->job_state as $stateName){
                $query->orWhere("jobslist.job_state", 'like', '%'.$stateName.'%');
            }
        }
        if ($request->has("job_seniority"))
            $query->where("jobslist.job_seniority", $request->job_seniority);
        if ($request->has("job_salary_start"))
            $query->where("jobslist.minimum_wage", ">=", $request->job_salary_start);
        if ($request->has("job_salary_end"))
            $query->where("jobslist.max_wage", "<=", $request->job_salary_end);
        if ($request->has("experience_in_months_start"))
            $query->where("jobslist.experience_in_months", ">=", $request->experience_in_months_start);
        if ($request->has("experience_in_months_end"))
            $query->where("jobslist.experience_in_months", "<=", $request->experience_in_months_end);
        if ($request->has("job_skills") && (is_array($request->job_skills) && !empty($request->job_skills)))
            $query->whereIn("skills.tag_id", $request->job_skills);
        if ($request->has("job_languages") && (is_array($request->job_languages) && !empty($request->job_languages)))
            $query->whereIn("languages.language_id", $request->job_languages);
        if ($request->has("job_visas") && (is_array($request->job_visas) && !empty($request->job_visas)))
            $query->whereIn("job_visas.visas_type_id", $request->job_visas);
        if ($request->has("job_visas_countries") && (is_array($request->job_visas_countries) && !empty($request->job_visas_countries)))
            $query->whereIn("job_visas.country_id", $request->job_visas_countries);
        if ($request->has("experience_in_months"))
            $query->where("jobslist.experience_in_months", $request->experience_in_months);
        if ($request->has("job_description"))
            $query->where("jobslist.job_description", 'like', '%'.$request->job_description.'%');
        if ($request->has("job_experience_description"))
            $query->where("jobslist.job_experience_description", 'like', '%'.$request->job_experience_description.'%');
        if ($request->has("profession_for_job"))
            $query->where("jobslist.profession_for_job", $request->profession_for_job);
        if ($request->has("payment_type"))
            $query->where("jobslist.payment_type", $request->payment_type);
        if ($request->has("job_contract"))
            $query->where("jobslist.job_contract", $request->job_contract);
        if ($request->has("working_visa"))
            $query->where("jobslist.working_visa", $request->working_visa);
        if ($request->has("job_period"))
            $query->where("jobslist.job_period", $request->job_period);
        if ($request->has("wage_currency"))
            $query->where("jobslist.wage_currency", $request->wage_currency);
        if ($request->has("job_driving_licenses")){
            foreach($request->job_driving_licenses as $drivingLicense){
                $query->orWhere("job_driving_licenses.job_driving_license", $drivingLicense);
            }
        }
        if ($request->has("job_certifications")){
            foreach($request->job_certifications as $certification){
                $query->orWhere("job_certifications.job_certification", $certification);
            }
        }
        if ($request->has("location")){
            $location = explode(',', $request->location);
            foreach($location as $term){
                $term = trim($term);
                $country = (new ListCountry)->getByNameAndLanguageIso($term, Session()->get('user_lang'));
                $query->where(function($query) use ($term, $country) {
                    $countryId = $country ? $country->lcountry_id : null;
                    $query->where('job_city', 'like', "%{$term}%")
                          ->orWhere('job_state', 'like', "%{$term}%")
                          ->orWhere('job_country', $countryId);
                });
            }
        }
        if ($request->has("free_term")){
            $attrs = ['jobslist.job_title'];
            foreach($attrs as $attr){
                $query->where($attr, 'like', '%'.$request->working_visa.'%');
            }
        }
        if($limit)
            $query->limit($limit);
        if($offset)
            $query->offset($offset);
        $query->orderBy('jobslist.created_at', 'desc');
        return $query->get();
    }

    /**
     * Gets a JobList object with all its data by id
     * @param Int jobId
     * @return JobList|Null
     */
    public function getJobDeatils($jobId = null)
    {
        $result = JobList::where('job_id', $jobId)->leftJoin('companies AS company', function($join){
            $join->on('jobslist.company_id', '=', 'company.company_id');
        })->leftJoin('job_skills AS skills', function($join){
            $join->on('jobslist.job_id', '=', 'skills.joblist_id');
        })->leftJoin('job_languages AS languages', function($join){
            $join->on('jobslist.job_id', '=', 'languages.joblist_id');
        })->leftJoin('job_visas AS job_visas', function($join){
            $join->on('jobslist.job_id', '=', 'job_visas.joblist_id');
        })->first();
        if(!$result)
            return null;
        $response = $this->gatherJobJoinData($result);
        return is_array($response) && !empty($response) ? $response[0] : null;
    }

    /**
     * Calculates the offset for the query
     * @param Array totals
     * @param Int perPage
     * @param Int coeficient
     * @return Array - schema: ['offset' => Array, 'limit' => Array, 'perPage' => Int, 'lastPage' => Int]
     */
    public function calculatePaginationData($totals = [], $page = 1, $perPage = 10, $coeficient = 5)
    {
        $payingCount    = array_key_exists('paying', $totals)    ? $totals['paying']    : 0;
        $nonPayingCount = array_key_exists('nonPaying', $totals) ? $totals['nonPaying'] : 0;
        $totalCompanies = $payingCount + $nonPayingCount;
        if($payingCount == $nonPayingCount)
            $totalCompanies = (int)ceil($totalCompanies / 2);
        $maxPages     = (int)ceil($totalCompanies / $perPage);
        $logics = [
            'paying'        => 0,
            'nonPaying'     => 0,
            'maxPages'      => $maxPages,
            'offset'        => [
                'paying'    => 0,
                'nonPaying' => 0
            ],
            'limit'         => [
                'paying'    => 0,
                'nonPaying' => 0
            ]
        ];
        $index = 0;
        for($i = 0; $i < $perPage; $i++){
            if($index < $coeficient){
                $logics['paying']++;
            }else{
                $logics['nonPaying']++;
                $index = 0;
            }
            $index++;
            if($index >= $perPage)
                break;
        }
        if($payingCount == 0){
            $logics['paying']    = 0;
            $logics['nonPaying'] = $nonPayingCount < $perPage ? $nonPayingCount : $perPage;
        }
        $lastPage = $maxPages;
        if($page > $lastPage)
            $page = $lastPage;
        if($page < 1)
            $page = 1;
        $logics['offset']['paying']    = $page == 1 ? 0 : (int)$page * $logics['paying'];
        $logics['offset']['nonPaying'] = $page == 1 ? 0 : (int)$page * $logics['paying'];
        if($logics['offset']['paying'] > $payingCount && $logics['offset']['nonPaying'] < $nonPayingCount){
            $logics['limit']['paying']    = 0;
            $logics['limit']['nonPaying'] = $perPage;
        }else if($logics['offset']['paying'] < $payingCount && $logics['offset']['nonPaying'] > $nonPayingCount){
            $logics['limit']['nonPaying'] = 0;
            $logics['limit']['paying']    = $perPage;
        }else{
            $logics['limit']['paying']    = $logics['paying'];
            $logics['limit']['nonPaying'] = $logics['nonPaying'];
        }

        $total = $totals['nonPaying'] + $totals['paying'];
        if($page == 1 && $total != $perPage){
            $logics['limit']['paying']    = $totals['paying'];
            $logics['limit']['nonPaying'] = $totals['nonPaying'];
        }
        return [
            'limit'     => $logics['limit'],
            'offset'    => $logics['offset'],
            'lastPage'  => $lastPage,
            'page'      => $page
        ];
    }

    /**
     * Get jobs paginated
     * @param request request
     * @param Int page
     * @param Int perPage
     * @param Int coeficient
     * @return Array
     */
    public function getPaginatedJobs($request, $page = 1, $perPage = 10, $coeficient = 5)
    {
        $nonPaying      = $this->listJobs($request, false);
        $totalNonPaying = count($nonPaying);
        // $paying = $this->listJobs($request, true);
        $paying = [];
        $totalPaying = count($paying);
        $paginationData = $this->calculatePaginationData([
            'nonPaying' => $totalNonPaying,
            'paying'    => $totalPaying
        ], $page, $perPage, $coeficient);

        $data['ids'] = [
            'job_modality_id'    => [],
            'job_country'        => [],
            'job_seniority'      => [],
            'payment_type'       => [],
            'job_contract'       => [],
            'job_period'         => [],
            'working_visa'       => [],
            'wage_currency'      => [],
            'profession_for_job' => [],
            'company_type'       => []
        ];
        $attrsToGatherIds = array_keys($data['ids']);

        $data['nonPaying']    = [];
        $data['nonPayingIds'] = [];
        if($totalNonPaying > 0){
            $nonPayingIndex = $paginationData['offset']['nonPaying'];
            for($i = 0; $i < $paginationData['limit']['nonPaying']; $i++){
                if(!empty($nonPaying[$nonPayingIndex])){
                    $job = $nonPaying[$nonPayingIndex];
                    foreach($attrsToGatherIds as $attr){
                        $val = $job->{$attr};
                        if(in_array($val, $data['ids'][$attr]))
                            continue;
                        $data['ids'][$attr][] = $val;
                    }
                    $data['nonPayingIds'][] = $job->job_id;
                    $data['nonPaying'][] = $job;
                }
                $nonPayingIndex++;
            }
            $nonPaying = $data['nonPaying'];
        }else{
            $nonPaying = [];
        }
        $data['paying']    = [];
        $data['payingIds'] = [];
        if($totalPaying > 0){
            $payingIndex = $paginationData['offset']['paying'];
            for($i = 0; $i < $paginationData['limit']['paying']; $i++){
                if(!empty($paying[$payingIndex])){
                    $job = $paying[$payingIndex];
                    foreach($attrsToGatherIds as $attr){
                        $val = $job->{$attr};
                        if(in_array($val, $data['ids'][$attr]))
                            continue;
                        $data['ids'][$attr][] = $val;
                    }
                    $data['payingIds'][] = $job->job_id;
                    $data['paying'][] = $job;
                }
                $payingIndex++;
            }
            $paying = $data['paying'];
        }else{
            $paying = [];
        }
        $bdData = $this->getJobListBdData($data['ids']);
        $nonPaying = $this->gatherJobJoinData($nonPaying, $bdData, $request);
        $paying    = $this->gatherJobJoinData($paying, $bdData, $request);
        $data      = $this->orderByMatch($paying, $nonPaying);
        $nonPaying = $data['nonPaying'];
        $paying    = $data['paying'];
        $jobs = [];
        $tracker = 0;
        $indexes = [
            'paying'    => 0,
            'nonPaying' => 0
        ];
        if($totalPaying > 0){
            for($i = 0; $i < $perPage; $i++){
                if($tracker == $coeficient){
                    if(!empty($nonPaying[$indexes['nonPaying']]))
                        $jobs[] = $nonPaying[$indexes['nonPaying']];
                    $tracker = 0;
                    $indexes['nonPaying']++;
                }else{
                    if(!empty($paying[$indexes['paying']]))
                        $jobs[] = $paying[$indexes['paying']];
                    $indexes['paying']++;
                    $tracker++;
                }
            }
        }
        while(count($jobs) < $perPage){
            if(!empty($nonPaying[$indexes['nonPaying']])){
                $jobs[] = $nonPaying[$indexes['nonPaying']];
                $indexes['nonPaying']++;
            }else{
                break;
            }
        }
        $paginationData['results'] = $jobs;
        return $paginationData;
    }

    /**
     * Loops each result of Joblist Object array and settes its corresponding language, skill and proficiency
     * @param Array jobListArray
     * @param Array bdData
     * @param Request searchParameters
     * @return Array
     */
    public function gatherJobJoinData($jobListArray = [], $bdData = [], $searchParameters = [])
    {
        $usedAttr = [];
        foreach($jobListArray as $job){
            $usedAttr[$job->job_id] = [
                'skillsIds' => [],
                'skills' => [],
                'languagesIds' => [],
                'languages' => [],
                'visasIds' => [],
                'visas' => [],
                'jobCertificationsIds' => [],
                'jobCertifications' => [],
                'jobDrivingLicensesIds' => [],
                'jobDrivingLicenses' => []
            ];
        }
        // Gather expected data
        $ids = array_keys($usedAttr);
        $jobLanguagesArray = ModelUtils::parseAsArrayWithAllLanguagesIsosAndTranslations(
            JobLanguage::whereIn('joblist_id', $ids)->leftJoin('listlangues', function($join){
                $join->on('listlangues.llangue_id', '=', 'job_languages.language_id');
            })->leftJoin('translations', function($join){
                $join->on('translations.en', '=', 'listlangues.llangue_name');
            })->get(), 
            ['job_language_id','language_id','proficiency_id'],
            'joblist_id'
        );
        $jobSkillsArray = ModelUtils::parseAsArrayWithAllLanguagesIsosAndTranslations(
            JobSkill::whereIn('joblist_id', $ids)->leftJoin('tags', function($join){
                $join->on('tags.tags_id', '=', 'job_skills.tag_id');
            })->leftJoin('translations', function($join){
                $join->on('translations.en', '=', 'tags.tags_name');
            })->get(), 
            ['job_skill_id','tag_id','proficiency_id'],
            'joblist_id'
        );
        $visaTypesArray = ModelUtils::parseAsArrayWithAllLanguagesIsosAndTranslations(
            JobVisa::whereIn('joblist_id', $ids)->leftJoin('type_visas', function($join){
                $join->on('type_visas.typevisas_id', '=', 'job_visas.visas_type_id');
            })->leftJoin('translations', function($join){
                $join->on('translations.en', '=', 'type_visas.type_name');
            })->get(), 
            ['typevisas_id','type_name','job_visa_id', 'country_id'],
            'joblist_id'
        );
        $jobDrivingLicenseArray = ModelUtils::parseAsArrayWithAllLanguagesIsosAndTranslations(
            JobDrivingLicense::whereIn('job_driving_licenses.job_id', $ids)->leftJoin('driving_licenses', function($join){
                $join->on('driving_licenses.driving_license', '=', 'job_driving_licenses.driving_license');
            })->leftJoin('translations', function($join){
                $join->on('translations.en', '=', 'driving_licenses.name');
            })->get(), 
            ['symbol','driving_license','job_driving_license'],
            'job_id'
        );
        $jobCertificationArray = ModelUtils::parseAsArrayWithAllLanguagesIsosAndTranslations(
            JobCertification::whereIn('job_certifications.joblist_id', $ids)->leftJoin('certification_types', function($join){
                $join->on('certification_types.certification_type', '=', 'job_certifications.certification_type');
            })->leftJoin('translations', function($join){
                $join->on('translations.en', '=', 'certification_types.name');
            })->get(), 
            ['job_certification','certification_type','job_certification'],
            'joblist_id'
        );
        $commonCurrencies = $bdData['commonCurrencies'];
        $countriesTranslated = $bdData['listCountriesTrans'];
        $languageIso = Session()->has('user_lang') ? Session()->get('user_lang') : ListLangue::DEFAULT_LANGUAGE;
        $defaultLanguage = ListLangue::DEFAULT_LANGUAGE;
        $officialLanguages = Translation::OFFICIAL_LANGUAGES;
        $simpleJoinData = [
            'professions' => 'profession_for_job', 'JobPaymentTypes' => 'payment_type', 'jobContracts' => 'job_contract', 
            'workingVisas' => 'working_visa', 'jobPeriods' => 'job_period', 'companyTypes' => 'company_type', 'proficienciesTrans' => 'job_seniority'
        ];
        // Preparing data to be consumed
        foreach($jobLanguagesArray as $jobId => $jobLanguages){
            foreach($jobLanguages as $jobLanguage){
                $translation = $jobLanguage[$languageIso] ? $jobLanguage[$languageIso] : $jobLanguage[$defaultLanguage];
                $proficiency = array_key_exists($jobLanguage['proficiency_id'], $bdData['proficienciesTrans']) ? $bdData['proficienciesTrans'][$jobLanguage['proficiency_id']] : '';
                if($proficiency){
                    $proficiencyTrans = $proficiency[$languageIso] ? $proficiency[$languageIso] : $proficiency[$defaultLanguage];
                }else{
                    $proficiencyTrans = '';
                }
                $usedAttr[$jobId]['languages'][] = ucfirst($translation) . ' / ' . ucfirst($proficiencyTrans);
                $usedAttr[$jobId]['languagesIds'][] = $jobLanguage['language_id'];
            }
        }
        foreach($jobSkillsArray as $jobId => $jobSkills){
            foreach($jobSkills as $jobSkill){
                $translation = $jobSkill[$languageIso] ? $jobSkill[$languageIso] : $jobSkill[$defaultLanguage];
                $proficiency = array_key_exists($jobSkill['proficiency_id'], $bdData['proficienciesTrans']) ? $bdData['proficienciesTrans'][$jobSkill['proficiency_id']] : '';
                if($proficiency){
                    $proficiencyTrans = $proficiency[$languageIso] ? $proficiency[$languageIso] : $proficiency[$defaultLanguage];
                }else{
                    $proficiencyTrans = '';
                }
                $usedAttr[$jobId]['skills'][] = ucfirst($translation);
                $usedAttr[$jobId]['skillsProficiency'][] = ucfirst($translation) . ' / ' . ucfirst($proficiencyTrans);
                $usedAttr[$jobId]['skillsIds'][] = $jobSkill['tag_id'];
            }
        }
        foreach($visaTypesArray as $jobId => $jobVisas){
            foreach($jobVisas as $jobVisa){
                $translation = $jobVisa[$languageIso] ? $jobVisa[$languageIso] : $jobVisa[$defaultLanguage];
                $country = array_key_exists($jobVisa['country_id'], $bdData['listCountriesTrans']) ? $bdData['listCountriesTrans'][$jobVisa['country_id']] : '';
                if($country){
                    $countryTrans = $country[$languageIso] ? $country[$languageIso] : $country[$defaultLanguage];
                }else{
                    $countryTrans = '';
                }
                $usedAttr[$jobId]['visas'][] = ucfirst($translation) . ' / ' . ucfirst($countryTrans);
                $usedAttr[$jobId]['visasIds'][] = $jobVisa['typevisas_id'];
            }
        }
        foreach($jobCertificationArray as $jobId => $certifications){
            foreach($certifications as $certification){
                $translation = $certification[$languageIso] ? $certification[$languageIso] : $certification[$defaultLanguage];
                $usedAttr[$jobId]['jobCertifications'][] = $translation;
                $usedAttr[$jobId]['jobCertificationsIds'][] = $certification['job_certification'];
            }
        }
        foreach($jobDrivingLicenseArray as $jobId => $drivingLicenses){
            foreach($drivingLicenses as $drivingLicense){
                $translation = $drivingLicense[$languageIso] ? $drivingLicense[$languageIso] : $drivingLicense[$defaultLanguage];
                $usedAttr[$jobId]['jobDrivingLicenses'][] = ucfirst($translation) ." / {$drivingLicense['symbol']}";
                $usedAttr[$jobId]['jobDrivingLicensesIds'][] = $drivingLicense['job_driving_license'];
            }
        }
        // Set prepared data to objects
        $results = [];
        $usedJobIds = [];
        $attrToUnset = [
            'job_skill_id','tag_id','proficiency_id','language_id','joblist_id','job_language_id','job_visa_id','visas_type_id',
            'contact_email','contact_name','contact_phone'
        ];
        foreach($jobListArray as $job){
            if(!array_key_exists($job->job_id, $usedAttr) || in_array($job->job_id, $usedJobIds))
                continue;
            $usedJobIds[] = $job->job_id;
            $values = $usedAttr[$job->job_id];
            $thisObj = $job;
            $thisObj->skills             = $values['skills'];
            $thisObj->skillsProficiency  = $values['skillsProficiency'];
            $thisObj->skillsIds          = $values['skillsIds'];
            $thisObj->languages          = $values['languages'];
            $thisObj->languagesIds       = $values['languagesIds'];
            $thisObj->visas              = $values['visas'];
            $thisObj->visasIds           = $values['visasIds'];
            $thisObj->drivingLicensesIds = $values['jobDrivingLicenses'];
            $thisObj->drivingLicenses    = $values['jobDrivingLicenses'];
            $thisObj->certificationsIds  = $values['jobCertificationsIds'];
            $thisObj->certifications     = $values['jobCertifications'];
            $thisObj->wage_currency_id   = $job->wage_currency;
            $thisObj->posted_at          = $this->getTimeSincePosted($job->job_created_at);
            $thisObj->exp_required       = $this->getRequiredExperience($job->experience_in_months);
            $thisObj->experience_required= $this->getRequiredExperience($job->experience_in_months, true);
            $thisObj->match              = $this->generateCompatilityMatchOfJob($thisObj, $searchParameters);
            $thisObj->job_created_at     = ModelUtils::parseDateByLanguage($job->job_created_at, false, $languageIso);
            $thisObj->job_updated_at     = ModelUtils::parseDateByLanguage($job->job_updated_at, false, $languageIso);
            $thisObj->job_language_name  = '';
            if(array_key_exists($thisObj->job_language, $bdData['listLanguages'])){
                $langData = $bdData['listLanguages'][$thisObj->job_language];
                $thisObj->job_language_name = $langData[$languageIso] ? $langData[$languageIso] : $langData[$defaultLanguage];
            }
            $location = $thisObj->job_city;
            if(array_key_exists($thisObj->job_country, $countriesTranslated)){
                $countryName = $countriesTranslated[$thisObj->job_country][$languageIso] ? $countriesTranslated[$thisObj->job_country][$languageIso] : $countriesTranslated[$thisObj->job_country]['en'];
                $thisObj->job_country = ucfirst($countryName);
                $location .= ', ' . $thisObj->job_country;
            }
            $thisObj->location = $location;
            $fullLocation = $thisObj->job_city;
            if($thisObj->job_state)
                $fullLocation .= ", {$thisObj->job_state}";
            $thisObj->full_location = "$fullLocation, {$thisObj->job_country}";
            $minimunWage = str_replace('.', ',', $thisObj->minimum_wage);
            $maxWage = str_replace('.', ',', $thisObj->max_wage);
            $thisObj->minimunWage = (float)$thisObj->minimum_wage;
            $thisObj->maxWage = (float)$thisObj->maxWage;
            if(array_key_exists($thisObj->wage_currency, $commonCurrencies)){
                $wage_currency_name = $commonCurrencies[$thisObj->wage_currency][$languageIso] ? $commonCurrencies[$thisObj->wage_currency][$languageIso] : ListLangue::DEFAULT_LANGUAGE;
                $symbol = $commonCurrencies[$thisObj->wage_currency]->currency_symbol;
                $thisObj->wage_currency = $symbol . ' / ' . $wage_currency_name;
                $thisObj->wage_currency_symbol = $symbol;
            }else{
                $thisObj->wage_currency = '';
                $thisObj->wage_currency_name = '';
                $thisObj->wage_currency_symbol = '$';
            }
            $thisObj->salary = $thisObj->wage_currency_symbol . ' ' . ($minimunWage ? $minimunWage : '0,00') . ' - ' . $thisObj->wage_currency_symbol . ' ' . ($maxWage ? $maxWage : '0,00');
            $jobModality = '';
            if(array_key_exists($thisObj->job_modality_id, $bdData['jobModalities'])){
                $thisBdData = $bdData['jobModalities'][$thisObj->job_modality_id];
                if(!in_array($languageIso, $officialLanguages)){
                    $arr = json_decode($thisBdData['unofficial_translations'], true);
                    $jobModality = (array_key_exists($languageIso, $arr) && $arr[$languageIso]) ? $arr[$languageIso] : $arr[$defaultLanguage];
                }else{
                    $jobModality = $thisBdData[$languageIso];
                }
            }
            $thisObj->job_modality = $jobModality;
            foreach($simpleJoinData as $bdDataName => $key){
                if(!empty($bdData[$bdDataName]) && !empty($bdData[$bdDataName][$thisObj->{$key}])){
                    $thisBdData = $bdData[$bdDataName][$thisObj->{$key}];
                    if(!in_array($languageIso, $officialLanguages)){
                        $arr = json_decode($thisBdData['unofficial_translations'], true);
                        $translation = (array_key_exists($languageIso, $arr) && $arr[$languageIso]) ? $arr[$languageIso] : $arr[$defaultLanguage];
                    }else{
                        $translation = $thisBdData[$languageIso];
                    }
                    $thisObj->{$key} = $translation;
                }else{
                    $thisObj->{$key} = '';
                }
            }
            foreach($attrToUnset as $attrName){
                unset($thisObj->{$attrName});
            }
            $thisObj->company_logo = base64_encode($thisObj->company_logo);
            $thisObj->company_cover_photo = base64_encode($thisObj->company_cover_photo);
            $results[] = $thisObj;
        }
        return $results;
    }

    /**
     * Returns a string representing the required experience for the job
     * @param Int experience_in_months
     * @param Bool fullText - to return full 'experience' word instead of 'exp'
     * @return String
     */
    public function getRequiredExperience($experience_in_months = 0, $fullText = false)
    {
        $requiredExperience = '';
        $years = round($experience_in_months / 12);
        if($years < 1){
            $monthTrans = $experience_in_months > 1 ? translate('months') : translate('month');
            $requiredExperience = $experience_in_months . ' ' . ucfirst($monthTrans) . ' ' . ($fullText ? ucfirst(translate('experience')) : ucfirst(translate('exp')));
        }else{
            $yearTrans = $years > 1 ? translate('years') : translate('year');
            $requiredExperience = $years . ' ' . ucfirst($yearTrans) . ' ' . ($fullText ? ucfirst(translate('experience')) : ucfirst(translate('exp')));
        }
        return $requiredExperience;
    }

    /**
     * Returns the time in days or months since job was created
     * @param String jobCreatedAt
     * @return String
     */
    public function getTimeSincePosted($jobCreatedAt = '')
    {
        if($jobCreatedAt == '')
            return '';
        $today = Carbon::now();
        $jobCreatedAt = Carbon::parse($jobCreatedAt);
        $inDays = abs((int)$today->diffInDays($jobCreatedAt));
        if($inDays > 90){
            $months = round($inDays / 30);
            return $months . 'm';
        }else{
            return $inDays . 'd';
        }
    }

    /**
     * Returns an array containing all possible data for Joblist
     * @param Array jobsIds
     * @return Array
     */
    public function getJobListBdData($ids = [])
    {
        $listLangObj = new ListLangue();
        $languagesIso = $listLangObj->getNotOficialLangsIso();
        $datas = [
            'jobModalities'   => ModelUtils::getTranslationsArray(
                new JobModality(), 'name', !empty($ids['job_modality_id']) ? $ids['job_modality_id'] : [], 'job_modality_id', $languagesIso
            ),
            'job_country'     => ModelUtils::getTranslationsArray(
                new ListCountry(), 'lcountry_name', !empty($ids['job_country']) ? $ids['job_country'] : [], 'lcountry_id', $languagesIso
            ),
            'JobPaymentTypes' => ModelUtils::getTranslationsArray(
                new JobPaymentType(), 'name', !empty($ids['payment_type']) ? $ids['payment_type'] : [], 'job_payment_type', $languagesIso
            ),
            'jobContracts'    => ModelUtils::getTranslationsArray(
                new JobContract(), 'name', !empty($ids['job_contract']) ? $ids['job_contract'] : [], 'job_contract', $languagesIso
            ),
            'jobPeriods'      => ModelUtils::getTranslationsArray(
                new JobPeriod(), 'name', !empty($ids['job_period']) ? $ids['job_period'] : [], 'job_period', $languagesIso
            ),
            'workingVisas'    => ModelUtils::getTranslationsArray(
                new WorkingVisa(), 'name', !empty($ids['working_visa']) ? $ids['working_visa'] : [] , 'working_visa', $languagesIso
            ),
            'commonCurrencies'=> ModelUtils::getTranslationsArray(
                new CommonCurrency(), 'currency_name', !empty($ids['wage_currency']) ? $ids['wage_currency'] : [], 'common_currency_id', $languagesIso, true
            ),
            'professions'     => ModelUtils::getTranslationsArray(
                new ListProfession(), 'profession_name', !empty($ids['profession_for_job']) ? $ids['profession_for_job'] : [], 'lprofession_id', $languagesIso
            ),
            'companyTypes'    => ModelUtils::getTranslationsArray(
                new CompanyType(), 'name', !empty($ids['company_type']) ? $ids['company_type'] : [], 'company_type_id', $languagesIso
            ),

            
            'listCountriesTrans' => ModelUtils::getTranslationsArray(
                new ListCountry(), 'lcountry_name', [], 'lcountry_id', $languagesIso
            ),
            'tags' => ModelUtils::getTranslationsArray(new Tag(), 'tags_name', null, null, $languagesIso),
            'proficienciesTrans' => ModelUtils::getTranslationsArray(new Proficiency(), 'proficiency_level', null, null, $languagesIso),
            'visaTypes' => ModelUtils::getTranslationsArray(new TypeVisas(), 'type_name', null, null, $languagesIso),
            'listLanguages' => ModelUtils::getTranslationsArray(new ListLangue(), 'llangue_name', null, null, $languagesIso),
        ];
        return $datas;
    }

    /**
     * Orders sent results by match
     * @param Array $paying
     * @param Array $nonPaying
     * @return Array schema: ['paying' => Array, 'nonPaying' => Array] 
     */
    public function orderByMatch($paying = [], $nonPaying = [])
    {
        $payingMatch = [];
        foreach($paying as $pay){
            $payingMatch[$pay->match][] = $pay;
        }
        $notpayingMatch = [];
        foreach($nonPaying as $pay){
            $notpayingMatch[$pay->match][] = $pay;
        }
        krsort($payingMatch);
        $paying = [];
        foreach($payingMatch as $pMatch){
            foreach($pMatch as $job){
                $paying[] = $job;
            }
        }
        krsort($notpayingMatch);
        $nonPaying = [];
        foreach($notpayingMatch as $pMatch){
            foreach($pMatch as $job){
                $nonPaying[] = $job;
            }
        }
        return [
            'paying' => $paying,
            'nonPaying' => $nonPaying
        ];
    }


    /**
     * Reads sent job list data to order jobs
     * @param Array data - Schema: ['paying' => [], 'nonPaying' => []]
     * @param Int notPayingCoeficient - default 5: Paying jobs until one nonPaying job to be added to list
     * @return Array ['results' => [], 'status' => ['paying' => 0, 'nonPaying' => 0]]
     */
    public function processListedJobs($data = [], $perPage = 10, $page = 1, $coeficient = 5)
    {
        $paying    = $data['paying'];
        $nonPaying = $data['nonPaying'];
        $total = count($paying) + count($nonPaying);
        $pages = ceil($total / $perPage);
        $tracker = 0;
        $results = [];
        $sizes = [
            'paying' => count($paying),
            'nonPaying' => count($nonPaying)
        ];
        $currPage = 1;
        $usedIds = [];
        while($currPage <= $pages){
            $index = 0;
            $results[$currPage] = [];
            foreach($paying as $payingJob){
                if(count($results[$currPage]) == $perPage)
                    break;
                if(in_array($payingJob->job_id, $usedIds))
                    continue;
                if($index == $coeficient){
                    if(count($results[$currPage]) == $perPage)
                        break;
                    if(in_array($nonPaying[$tracker]->job_id, $usedIds)){
                        $tracker++;
                        continue;
                    }
                    if(empty($nonPaying[$tracker]))
                        break;
                    $results[$currPage][] = $nonPaying[$tracker];
                    $usedIds[] = $nonPaying[$tracker]->job_id;
                    $index = 0;
                    $tracker++;
                }
                $results[$currPage][] = $payingJob;
                $usedIds[] = $payingJob->job_id;
                $index++;
            }
            if(!array_key_exists($currPage, $results))
                $results[$currPage] = [];
            if(count($results[$currPage]) < $perPage){
                while($tracker <= $sizes['nonPaying']){
                    if(empty($nonPaying[$tracker]))
                        break;
                    if(in_array($nonPaying[$tracker]->job_id, $usedIds)){
                        $tracker++;
                        continue;
                    }
                    if(count($results[$currPage]) >= $perPage)
                        break;
                    if(empty($nonPaying[$tracker]))
                        break;
                    $results[$currPage][] = $nonPaying[$tracker];
                    $usedIds[] = $nonPaying[$tracker]->job_id;
                    $tracker++;
                }
            }
            $currPage++;
        }
        return [
            'results' => $results,
            'total' => (int)$total,
            'per_page' => (int)$perPage,
            'last_page' => (int)$pages,
            'curent_page' => (int)$page,
        ];
    }

    /**
     * Returns a number wiht the match of this Job by Request parametes
     * @param JobList $jobList
     * @param \Illuminate\Http\Request $parameters
     * @return Float|Null
     */
    public function generateCompatilityMatchOfJob($jobList, $parameters = null)
    {
        if(!$parameters || !is_object($parameters))
            return null;
        $validParameters = [
            'equal'   => [],
            'like'    => ['job_description', 'job_experience_description'],
            'in'      => ['minimum_wage::job_salary_start|job_salary_end', 'max_wage::job_salary_start|job_salary_end', 'experience_in_months::experience_in_months_start|experience_in_months_end'],
            'inArray' => ['company_id', 'job_modality_id', 'job_city', 'job_country', 'job_seniority', 'job_driving_licenses', 'job_certifications'],
            'many'    => ['job_skills::tags_id', 'job_languages::llangue_id', 'job_visas::typevisas_id', 'job_visas_countries::lcountry_id']
        ];
        $match = 0;
        $totalKeys = 0;
        // Gather all parameters
        foreach($validParameters as $checkType => $keys){
            foreach($keys as $key){
                if($checkType == 'many'){
                    $data = explode('::', $key);
                    $keyName = $data[0];
                    $keyValue = $parameters->has($keyName) ? $parameters->{$keyName} : null; 
                }else if($checkType == 'in'){
                    $data = explode('::', $key);
                    $keyValue = '';
                    if(!empty($data[1])){
                        foreach(explode('|', $data[1]) as $val){
                            $keyValue = $parameters->has($val) ? $parameters->{$val} : null;
                            if($keyValue){
                                $totalKeys++;
                            }
                        }
                    }
                    continue;
                }else{
                    $keyValue = $parameters->has($key) ? $parameters->{$key} : null;
                }
                if(!$keyValue)
                    continue;
                $totalKeys++;
            }
        }
        $matchCoeficient = $totalKeys > 0 ? $matchCoeficient = 100 / $totalKeys : 100;
        if($totalKeys == 0)
            return 100;
        // Read values
        foreach($validParameters as $checkType => $keys){
            foreach($keys as $key){
                if($checkType == 'many'){
                    $data = explode('::', $key);
                    $keyName = $data[0];
                    $keyId = count($data) == 2 ? $data[1] : null;
                    $keyValue = $parameters->has($keyName) ? $parameters->{$keyName} : null; 
                }else if($checkType == 'in'){
                    $data = explode('::', $key);
                    $keyValue = '';
                    if(!empty($data[1])){
                        foreach(explode('|', $data[1]) as $paramName){
                            $paramValue = $parameters->has($paramName) ? $parameters->{$paramName} : null;
                            if($paramValue)
                                $keyValue = true;
                        }
                        if($keyValue)
                            $keyValue = $data;
                    }
                }else{
                    $keyValue = $parameters->has($key) ? $parameters->{$key} : null;
                }
                if(!$keyValue)
                    continue;
                switch($checkType){
                    case 'equal':
                        if($keyValue == $jobList->{$key})
                            $match = $match + $matchCoeficient;
                    break;
                    case 'like':
                        if(is_numeric(strpos($jobList->{$key}, $keyValue)))
                            $match = $match + $matchCoeficient;
                    break;
                    case 'in':
                        $key = $keyValue[0];
                        $attr = explode('|', $keyValue[1]);
                        if(count($attr) != 2)
                            break;
                        $fromValue = (float)$parameters->{$attr[0]};
                        $toValue   = (float)$parameters->{$attr[1]};
                        $val       = (float)$jobList->{$key};
                        if(!$fromValue || !$toValue){
                            if(($fromValue && $val <= $fromValue) || ($toValue && $val >= $toValue))
                                $match = $match + $matchCoeficient;
                        }else if($fromValue <= $val && $toValue >= $val){
                            $match = $match + ($matchCoeficient * 2);
                        }
                    break;
                    case 'many':
                        if(!is_array($keyValue) || count($keyValue) < 1)
                            break;
                        $valid = [];
                        $attrName = str_replace('job_', '', $keyName);
                        $objectValue = $jobList->{$attrName . 'Ids'};
                        foreach($objectValue as $valObj){
                            if(in_array($valObj, $keyValue))
                                $valid[] = $valObj;
                        }
                        $totalSize = count($keyValue);
                        $thisVal = ($matchCoeficient / $totalSize) * count($valid);
                        $match = $match + $thisVal;
                    break;
                    case 'inArray':
                        $objectValue = $jobList->{$key};
                        $thisVal = $matchCoeficient / count($keyValue);
                        if(in_array($objectValue, $keyValue)){
                            $match = $match + $thisVal;
                        }
                    break;
                }
            }
        }
        $matchValue = number_format((float)$match, 2, '.', '');
        $matchValue = $matchValue > 100 ? 100 : $matchValue;
        return $matchValue < 0 ? 0 : $matchValue;
    }

    /**
     * Syncs job skills by deleting all skills of $this JobList and them adding the sents skills
     * @param Array $skills - Array of object Tag
     * @return Bool
     */
    public function syncSkills($skills = [])
    {
        JobSkill::where('joblist_id', $this->job_id)->delete();
        foreach($skills as $skill){
            JobSkill::create([
                'joblist_id' => $this->job_id,
                'tag_id' => $skill->tags_id
            ]);
        }
        return true;
    }

    /**
     * Syncs job languages by deleting all languages of $this JobList and them adding the sents Languages
     * @param Array $languages - Array of object Language
     * @return Bool
     */
    public function syncLanguages($languages = [])
    {
        JobLanguage::where('joblist_id', $this->job_id)->delete();
        foreach($languages as $language){
            JobLanguage::create([
                'joblist_id' => $this->job_id,
                'language_id' => $language->llangue_id,
                'proficiency_id' => $language->proficiency_id
            ]);
        }
        return true;
    }

    /**
     * Syncs job visas by deleting all visas of $this JobList and them adding the sents visas
     * @param Array $visas - Array of object Visas
     * @return Bool
     */
    public function syncVisas($visas = [])
    {
        JobVisa::where('joblist_id', $this->job_id)->delete();
        foreach($visas as $visa){
            JobVisa::create([
                'joblist_id' => $this->job_id,
                'visas_type_id' => $visa->visas_type_id,
                'country_id' => $visa->country_id
            ]);
        }
        return true;
    }

    /**
     * Syncs job certifications by deleting all JobCertifications of $this JobList and them adding the sents CertificationTypes
     * @param Array $certifications - Array of object CertificationType
     * @return Bool
     */
    public function syncCertifications($certifications = [])
    {
        JobCertification::where('joblist_id', $this->job_id)->delete();
        foreach($certifications as $certification){
            JobCertification::create([
                'joblist_id' => $this->job_id,
                'certification_type' => $certification->certification_type
            ]);
        }
        return true;
    }

    /**
     * Syncs job driving licenses by deleting all visas of $this JobList and them adding the sents driving licenses
     * @param Array $drivingLicenses - Array of object DrivingLicense
     * @return Bool
     */
    public function syncDrivingLicenses($drivingLicenses = [])
    {
        JobDrivingLicense::where('job_id', $this->job_id)->delete();
        foreach($drivingLicenses as $drivingLicense){
            JobDrivingLicense::create([
                'job_id' => $this->job_id,
                'driving_license' => $drivingLicense->driving_license,
            ]);
        }
        return true;
    }

    /**
     * Gets a job
     * @param Int id
     * @return JobList
     */
    public function getJob($id = null)
    {
        $query = JobList::select(
            'jobslist.job_id', 'jobslist.created_at', 'jobslist.*', 'company.*', 'jobslist.created_at AS job_created_at', 'jobslist.updated_at AS job_updated_at'
        )->leftJoin('companies AS company', function($join){
            $join->on('jobslist.company_id', '=', 'company.company_id');
        })->leftJoin('job_skills AS skills', function($join){
            $join->on('jobslist.job_id', '=', 'skills.joblist_id');
        })->leftJoin('job_languages AS languages', function($join){
            $join->on('jobslist.job_id', '=', 'languages.joblist_id');
        })->leftJoin('job_visas AS job_visas', function($join){
            $join->on('jobslist.job_id', '=', 'job_visas.joblist_id');
        })->leftJoin('job_driving_licenses', function($join){
            $join->on('jobslist.job_id', '=', 'job_driving_licenses.job_id');
        })->leftJoin('job_certifications', function($join){
            $join->on('jobslist.job_id', '=', 'job_certifications.joblist_id');
        })->where('jobslist.job_id', $id);
        return $id ? $query->first() : $query->get();
    }
}
