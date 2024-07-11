<?php

namespace App\Models;

use App\Helpers\ModelUtils;
use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobList extends Model
{
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
        'job_salary',
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
        'wage_currency'
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
     *      'working_visa' => integer, 'job_period' => integer, 'wage_currency' => integer
     * ]
     * @param Bool paying
     * @param Int limit
     * @param Int offset
     * @return Array
     */
    public function listJobs(\Illuminate\Http\Request $request, $paying = false, $limit = 200, $offset = null, $distinct = true, $byIds = [])
    {
        $limit = !is_numeric($limit) || $limit > 200 ? 200 : $limit;
        if($distinct){
            $query = JobList::select(
                'jobslist.job_id', 'jobslist.created_at', 'jobslist.*', 'company.*', 'jobslist.created_at AS job_created_at', 'jobslist.updated_at AS job_updated_at'
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
        })->where('company.paying', $paying);
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
            $query->whereIn("jobslist.job_seniority", $request->job_seniority);
        if ($request->has("job_salary_start"))
            $query->where("jobslist.job_salary", ">=", $request->job_salary_start);
        if ($request->has("job_salary_end"))
            $query->where("jobslist.job_salary", "<=", $request->job_salary_end);
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
     * Loops each result of Joblist Object array and settes its corresponding language, skill and proficiency
     * @param Array jobListArray
     * @param Array bdData
     * @param Request searchParameters
     * @return Array
     */
    public function gatherJobJoinData($jobListArray = [], $bdData = [], $searchParameters = [])
    {
        // Prepare array and define job ids
        $usedAttr = [];
        foreach($jobListArray as $job){
            $usedAttr[$job->job_id] = [
                'skillsIds' => [],
                'skills' => [],
                'languagesIds' => [],
                'languages' => [],
                'visasIds' => [],
                'visas' => []
            ];
        }
        // Gather expected data
        $ids = array_keys($usedAttr);
        $jobLanguagesArray = JobLanguage::whereIn('joblist_id', $ids)->leftJoin('jobslist', function($join){
            $join->on('jobslist.job_id', '=', 'job_languages.joblist_id');
        })->leftJoin('listlangues', function($join){
            $join->on('listlangues.llangue_id', '=', 'job_languages.language_id');
        })->leftJoin('proficiency', function($join){
            $join->on('proficiency.proficiency_id', '=', 'job_languages.proficiency_id');
        })->get();
        $jobSkillsArray = JobSkill::whereIn('joblist_id', $ids)->leftJoin('jobslist', function($join){
            $join->on('jobslist.job_id', '=', 'job_skills.joblist_id');
        })->leftJoin('tags', function($join){
            $join->on('tags.tags_id', '=', 'job_skills.tag_id');
        })->get();
        $visaTypesArray = JobVisa::whereIn('joblist_id', $ids)->leftJoin('jobslist', function($join){
            $join->on('jobslist.job_id', '=', 'job_visas.joblist_id');
        })->leftJoin('type_visas', function($join){
            $join->on('type_visas.typevisas_id', '=', 'job_visas.visas_type_id');
        })->get();

        $tags = $bdData['tags'];
        $proficiencies = $bdData['proficiencies'];
        $visaTypes = $bdData['visaTypes'];
        $listLanguages = $bdData['listLanguages'];
        $commonCurrencies = $bdData['commonCurrencies'];
        $listCountries = $bdData['listCountries'];
        $countriesTranslated = $bdData['countriesTranslated'];
        $languageIso = Session()->has('user_lang') ? Session()->get('user_lang') : ListLangue::DEFAULT_LANGUAGE;
        $defaultLanguage = ListLangue::DEFAULT_LANGUAGE;
        $officialLanguages = Translation::OFFICIAL_LANGUAGES;
        $simpleJoinData = [
            'professions' => 'profession_for_job', 'JobPaymentTypes' => 'payment_type', 'jobContracts' => 'job_contract', 
            'workingVisas' => 'working_visa', 'jobPeriods' => 'job_period', 'companyTypes' => 'company_type', 
            'listCountries' => 'job_country', 'proficiencies' => 'job_seniority'
        ];
        // Preparing data to be consumed
        foreach($jobLanguagesArray as $jobLanguage){
            if(!in_array($jobLanguage->joblist_id, $usedAttr[$jobLanguage->joblist_id]['languagesIds'])){
                $data = ModelUtils::getFillableData($jobLanguage, true, [
                    'proficiency_id' => ['objects' => $proficiencies, 'translated' => true, 'to' => 'proficiency'],
                    'language_id' => ['objects' => $listLanguages, 'translated' => true, 'to' => 'language']
                ]);
                $proficiencyLang = $data['proficiency'][$languageIso] ? $data['proficiency'][$languageIso] : $data['proficiency'][ListLangue::DEFAULT_LANGUAGE];
                $languageLang = $data['language'][$languageIso] ? $data['language'][$languageIso] : $data['language'][ListLangue::DEFAULT_LANGUAGE];
                $usedAttr[$jobLanguage->joblist_id]['languages'][] = ucfirst($languageLang) . ' / ' . ucfirst($proficiencyLang);
                $usedAttr[$jobLanguage->joblist_id]['languagesIds'][] = $data['language_id'];
            }
        }
        foreach($jobSkillsArray as $jobSkill){
            if(!in_array($jobSkill->joblist_id, $usedAttr[$jobSkill->joblist_id]['skillsIds'])){
                $data = ModelUtils::getFillableData($jobSkill, true, [
                    'tag_id' => ['objects' => $tags, 'translated' => true, 'to' => 'tag'],
                    'proficiency_id' => ['objects' => $proficiencies, 'translated' => true, 'to' => 'proficiency']
                ]);
                $tagLang = $data['tag'][$languageIso] ? $data['tag'][$languageIso] : $data['tag'][ListLangue::DEFAULT_LANGUAGE];
                $proficiencyLang = $data['proficiency'][$languageIso] ? $data['proficiency'][$languageIso] : $data['proficiency'][ListLangue::DEFAULT_LANGUAGE];
                $usedAttr[$jobSkill->joblist_id]['skills'][] = ucfirst($tagLang) . ' / ' . ucfirst($proficiencyLang);
                $usedAttr[$jobSkill->joblist_id]['skillsIds'][] = $data['tag_id'];
            }
        }
        foreach($visaTypesArray as $jobVisa){
            if(!in_array($jobVisa->joblist_id, $usedAttr[$jobVisa->joblist_id]['visasIds'])){
                $data = ModelUtils::getFillableData($jobVisa, true, [
                    'visas_type_id' => ['objects' => $visaTypes, 'translated' => true, 'to' => 'visa_type'],
                    'country_id' => ['objects' => $listCountries, 'translated' => true, 'to' => 'country'],
                ]);
                $visaTypeLang = $data['visa_type'][$languageIso] ? $data['visa_type'][$languageIso] : $data['visa_type'][ListLangue::DEFAULT_LANGUAGE];
                $countryLang = $data['country'][$languageIso] ? $data['country'][$languageIso] : $data['country'][ListLangue::DEFAULT_LANGUAGE];
                $usedAttr[$jobVisa->joblist_id]['visas'][] = ucfirst($visaTypeLang) . ' / ' . ucfirst($countryLang);
                $usedAttr[$jobVisa->joblist_id]['visasIds'][] = $data['visas_type_id'];
            }
        }
        // Set prepared data to objects
        $results = [];
        $usedJobIds = [];
        $attrToUnset = ['job_skill_id','tag_id','proficiency_id','language_id','country_id','joblist_id','job_language_id','job_visa_id','visas_type_id','job_country','job_modality_id'];
        foreach($jobListArray as $job){
            if(!array_key_exists($job->job_id, $usedAttr) || in_array($job->job_id, $usedJobIds))
                continue;
            $usedJobIds[] = $job->job_id;
            $values = $usedAttr[$job->job_id];
            $thisObj = $job;
            $thisObj->skills         = $values['skills'];
            $thisObj->skillsIds      = $values['skillsIds'];
            $thisObj->languages      = $values['languages'];
            $thisObj->languagesIds   = $values['languagesIds'];
            $thisObj->visas          = $values['visas'];
            $thisObj->visasIds       = $values['visasIds'];
            $thisObj->match          = $this->generateCompatilityMatchOfJob($thisObj, $searchParameters);
            $thisObj->job_created_at = ModelUtils::parseDateByLanguage($job->job_created_at, false, $languageIso);
            $thisObj->job_updated_at = ModelUtils::parseDateByLanguage($job->job_updated_at, false, $languageIso);
            $location = rtrim(trim($thisObj->job_city . ' | ' . $thisObj->job_state), ' | ');
            if(array_key_exists($thisObj->job_country, $countriesTranslated)){
                $location .= ' / ' . $countriesTranslated[$thisObj->job_country][$languageIso] ? $countriesTranslated[$thisObj->job_country][$languageIso] : $countriesTranslated[$thisObj->job_country]['en'];
            }
            $thisObj->location = $location;
            $thisObj->salary = '$' . str_replace('.', ',', $thisObj->job_salary);
            if(array_key_exists($thisObj->wage_currency, $commonCurrencies)){
                $wage_currency_name = $commonCurrencies[$thisObj->wage_currency][$languageIso] ? $commonCurrencies[$thisObj->wage_currency][$languageIso] : ListLangue::DEFAULT_LANGUAGE;
                $thisObj->wage_currency = $commonCurrencies[$thisObj->wage_currency]->currency_symbol . ' / ' . $wage_currency_name;
            }else{
                $thisObj->wage_currency = '';
                $thisObj->wage_currency_name = '';
            }
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
            'in'      => ['job_salary::job_salary_start|job_salary_end', 'experience_in_months::experience_in_months_start|experience_in_months_end'],
            'inArray' => ['company_id', 'job_modality_id', 'job_city', 'job_country', 'job_seniority'],
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
    public function sycnSkills($skills = [])
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
    public function sycnLanguages($languages = [])
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
    public function sycnVisas($visas = [])
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
}
