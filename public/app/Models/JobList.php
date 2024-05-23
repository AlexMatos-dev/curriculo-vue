<?php

namespace App\Models;

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
        'job_city',
        'job_country',
        'job_seniority',
        'job_salary',
        'job_description',
        'job_experience_description',
        'experience_in_months',
        'job_benefits'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function city()
    {
        return $this->belongsTo(ListCity::class, 'city_id');
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
     *      'job_city' => int, 'job_seniority' => int, 'job_salary_start' => float, 'job_salary_end' => float,
     *      'experience_in_months_start' => integer, 'experience_in_months_end' => integer, 'job_skills' => IntArray,
     * ]
     * @param Bool paying
     * @param Int limit
     * @param Int offset
     * @return Array
     */
    public function listJobs(\Illuminate\Http\Request $request, $paying = false, $limit = 100, $offset = null)
    {
        $limit = !is_numeric($limit) ? 100 : $limit;
        $limit = $limit > 100 ? 100 : $limit;
        $limit = $limit < 0 ? 1 : $limit;
        $query = JobList::leftJoin('companies AS company', function($join){
            $join->on('jobslist.company_id', '=', 'company.company_id');
        })->leftJoin('job_skills AS skills', function($join){
            $join->on('jobslist.job_id', '=', 'skills.joblist_id');
        })->leftJoin('job_languages AS languages', function($join){
            $join->on('jobslist.job_id', '=', 'languages.joblist_id');
        })->leftJoin('job_visas AS job_visas', function($join){
            $join->on('jobslist.job_id', '=', 'job_visas.joblist_id');
        })->where('company.paying', $paying);
        if ($request->has("company_id")) 
            $query->whereIn("jobslist.company_id", $request->company_id);
        if ($request->has("job_modality_id")) 
            $query->whereIn("jobslist.job_modality_id", $request->job_modality_id);
        if ($request->has("job_country"))
            $query->whereIn("jobslist.job_country", $request->job_country);
        if ($request->has("job_city"))
            $query->whereIn("jobslist.job_city", $request->job_city);
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
        $response = $this->splitjoinDataFromListedJobs($result);
        return is_array($response) && !empty($response) ? $response[0] : null;
    }

    public function splitjoinDataFromListedJobs($jobListArray = [])
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
        // Preparing data to be consumed
        foreach($jobLanguagesArray as $jobLanguage){
            if(!in_array($jobLanguage->joblist_id, $usedAttr[$jobLanguage->joblist_id]['languagesIds'])){
                $usedAttr[$jobLanguage->joblist_id]['languages'][] = $jobLanguage;
                $usedAttr[$jobLanguage->joblist_id]['languagesIds'][] = $jobLanguage->job_language_id; 
            }
        }
        foreach($jobSkillsArray as $jobSkill){
            if(!in_array($jobSkill->joblist_id, $usedAttr[$jobSkill->joblist_id]['skillsIds'])){
                $usedAttr[$jobSkill->joblist_id]['skills'][] = $jobSkill;
                $usedAttr[$jobSkill->joblist_id]['skillsIds'][] = $jobSkill->job_language_id; 
            }
        }
        foreach($visaTypesArray as $jobVisa){
            if(!in_array($jobVisa->joblist_id, $usedAttr[$jobVisa->joblist_id]['visasIds'])){
                $usedAttr[$jobVisa->joblist_id]['visas'][] = $jobVisa;
                $usedAttr[$jobVisa->joblist_id]['visasIds'][] = $jobVisa->job_visa_id; 
            }
        }
        // Set prepared data to objects
        $results = [];
        $usedJobIds = [];
        foreach($jobListArray as $job){
            if(!array_key_exists($job->job_id, $usedAttr) || in_array($job->job_id, $usedJobIds))
                continue;
            $usedJobIds[] = $job->job_id;
            $values = $usedAttr[$job->job_id];
            $thisObj = $job;
            $thisObj->skills    = $values['skills'];
            $thisObj->languages = $values['languages'];
            $thisObj->visas     = $values['visas'];
            $results[] = $thisObj;
        }
        return $results;
    }

    /**
     * Reads sent job list data to order jobs
     * @param Array data - Schema: ['paying' => [], 'nonPaying' => []]
     * @param Int maxJobs - default: 100: Max results to return
     * @param Int notPayingCoeficient - default 5: Paying jobs until one nonPaying job to be added to list
     * @return Array ['results' => [], 'status' => ['paying' => 0, 'nonPaying' => 0]]
     */
    public function processListedJobs($data = [], $maxJobs = 100, \Illuminate\Http\Request $searchParameters = null, $notPayingCoeficient = 5)
    {
        $results = [];
        $tracker = 1;
        $totalJobs = 0;
        $notPayingIndex = 0;
        $status = [
            'paying' => 0,
            'nonPaying' => 0
        ];
        $repeated = [];
        $paying = [];
        foreach($data['paying'] as $payingJob){
            if(in_array($payingJob->job_id, $repeated))
                continue;
            $repeated[] = $payingJob->job_id;
            $payingJob->match = $this->generateCompatilityMatchOfJob($payingJob, $searchParameters);
            $paying[] = $payingJob;
        }
        $nonPaying = [];
        foreach($data['nonPaying'] as $nonPayingJob){
            if(in_array($nonPayingJob->job_id, $repeated))
                continue;
            $repeated[] = $nonPayingJob->job_id;
            $nonPayingJob->match = $this->generateCompatilityMatchOfJob($nonPayingJob, $searchParameters);
            $nonPaying[] = $nonPayingJob;
        }
        if(empty($paying)){
            $results = $nonPaying;
        }else if(count($paying) < $notPayingCoeficient){
            $results = $paying;
            $index = count($paying);
            foreach($nonPaying as $notPayingJob){
                if($index == $maxJobs)
                    break;
                $results[] = $notPayingJob;
                $index++;
            }
        }else{
            foreach($paying as $payingJob){
                if($tracker == $notPayingCoeficient){
                    if($totalJobs == $maxJobs)
                        break;
                    $tracker = 1;
                    $nonPayingJob = !empty($nonPaying[$notPayingIndex]) ? $nonPaying[$notPayingIndex] : null;
                    if($nonPayingJob){
                        $results[] = $nonPayingJob;
                        $notPayingIndex++;
                        $totalJobs++;
                        $status['nonPaying']++;
                    }
                }
                if($totalJobs == $maxJobs)
                    break;
                $results[] = $payingJob;
                $totalJobs++;
                $tracker++;
                $status['paying']++;
            }
        }
        if($results < $maxJobs){
            foreach($nonPaying as $nonPayingJob){
                if($totalJobs == $maxJobs)
                    break;
                $results[] = $nonPayingJob;
                $totalJobs++;
            }
        }
        return [
            'results' => $results,
            'status'  => $status
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
                    $keyId = count($data) == 2 ? $data[1] : null;
                    $keyValue = $parameters->has($keyName) ? $parameters->{$keyName} : null; 
                }else{
                    $keyValue = $parameters->has($key) ? $parameters->{$key} : null;
                }
                if(!$keyValue)
                    continue;
                $totalKeys++;
            }
        }
        $matchCoeficient = $totalKeys > 0 ? $matchCoeficient = 100 / $totalKeys : 100;
        // Read values
        foreach($validParameters as $checkType => $keys){
            foreach($keys as $key){
                if($checkType == 'many'){
                    $data = explode('::', $key);
                    $keyName = $data[0];
                    $keyId = count($data) == 2 ? $data[1] : null;
                    $keyValue = $parameters->has($keyName) ? $parameters->{$keyName} : null; 
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
                        $dataArray = explode('::', $keyValue);
                        if(count($dataArray) != 2)
                            break;
                        $values = explode('|', $dataArray[1]);
                        if(count($values) != 2)
                            break;
                        $attr = $dataArray[0];
                        $fromValue = $values[0];
                        $toValue   = $values[1];
                        if(!$fromValue || !$toValue)
                            $match = $match + ($matchCoeficient / 2);
                        if($fromValue >= $jobList->{$attr} && $toValue <= $jobList->{$attr})
                            $match = $match + ($matchCoeficient / 2);
                    break;
                    case 'many':
                        if(!is_array($keyValue) || count($keyValue) < 1)
                            break;
                        $valid = [];
                        $attrName = str_replace('job_', '', $keyName);
                        $objectValue = $jobList->{$attrName};
                        foreach($objectValue as $valObj){
                            if(in_array($valObj->{$keyId}, $keyValue))
                                $valid[] = $valObj->{$keyId};
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