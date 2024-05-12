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
        'job_skills',
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
    public function listJobs(\Illuminate\Http\Request $request, $paying = false, $limit = null, $offset = null)
    {
        $query = JobList::leftJoin('companies AS company', function($join){
            $join->on('jobslist.company_id', '=', 'company.company_id');
        })->leftJoin('job_skills AS skills', function($join){
            $join->on('jobslist.job_id', '=', 'skills.joblist_id');
        })->leftJoin('job_languages AS languages', function($join){
            $join->on('jobslist.job_id', '=', 'languages.joblist_id');
        })->leftJoin('job_visas AS job_visas', function($join){
            $join->on('jobslist.job_id', '=', 'job_visas.joblist_id');
        })->where('company.paying', $paying);
        if ($request->has("job_modality_id")) 
            $query->where("jobslist.job_modality_id", $request->job_modality_id);
        if ($request->has("job_country"))
            $query->where("jobslist.job_country", $request->job_country);
        if ($request->has("job_city"))
            $query->where("jobslist.job_city", $request->job_city);
        if ($request->has("job_seniority"))
            $query->where("jobslist.job_seniority", $request->job_seniority);
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
    public function processListedJobs($data = [], $maxJobs = 100, $notPayingCoeficient = 5)
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
            $paying[] = $payingJob;
        }
        $nonPaying = [];
        foreach($data['nonPaying'] as $payingJob){
            if(in_array($payingJob->job_id, $repeated))
                continue;
            $repeated[] = $payingJob->job_id;
            $nonPaying[] = $payingJob;
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
}