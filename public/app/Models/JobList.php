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
        'job_english_level',
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
     *      'job_city' => int, 'job_seniority' => int, 'job_salary_start' => float, 'job_salary_end' => float, 'job_english_level' => int,
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
        if ($request->has("job_english_level"))
            $query->where("jobslist.job_english_level", $request->job_english_level);
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
        })->limit(1);
        if(!$result)
            return null;
        $response = $this->spliteJobSkillsFromListedJobs($result);
        return is_array($response) && !empty($response) ? $response[0] : null;
    }

    /**
     * Gathers skills from Jobs and filter result to not repeat them and set their skills
     * @param Array jobListArray - of JobList objects
     * @return Array of JobList objects
     */
    public function spliteJobSkillsFromListedJobs($jobListArray = [])
    {
        $used = [];
        $filtered = [];
        foreach($jobListArray as $job){
            if(array_key_exists($job->job_id, $filtered) && in_array($job->job_id, $used))
                continue;
            $filtered[$job->job_id][] = new JobSkill([
                'job_skill_id' => $job->job_skill_id,
                'joblist_id' => $job->joblist_id,
                'tag_id' => $job->tag_id,
            ]);
            $used[$job->job_id] = $job->tag_id;
        }
        $results = [];
        foreach($jobListArray as $job){
            if(in_array($job->job_id, $used))
                continue;
            $thisObj = $job;
            $thisObj->skills = array_key_exists($job->job_id, $filtered) ? $filtered[$job->job_id] : [];
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
        $paying = $data['paying'];
        $nonPaying = $data['nonPaying'];

        $results = [];
        $tracker = 1;
        $totalJobs = 0;
        $notPayingIndex = 0;
        $status = [
            'paying' => 0,
            'nonPaying' => 0
        ];
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