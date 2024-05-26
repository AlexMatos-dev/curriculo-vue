<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplied extends Model
{
    use SoftDeletes;
    
    const STATUS_APPLIED    = 'applied';
    const STATUS_VALIDATION = 'validating';
    const STATUS_REFUSED    = 'refused';
    const STATUS_ACCEPTED   = 'accepted';

    protected $primaryKey = 'applied_id';
    protected $table = 'jobs_applieds';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'professional_id',
        'status'
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'professional_id');
    }

    public function job()
    {
        return $this->belongsTo(JobList::class, 'job_id');
    }

    public function getStatus()
    {
        return [
            $this::STATUS_APPLIED    => $this::STATUS_APPLIED,
            $this::STATUS_VALIDATION => $this::STATUS_VALIDATION,
            $this::STATUS_REFUSED    => $this::STATUS_REFUSED,
            $this::STATUS_ACCEPTED   => $this::STATUS_ACCEPTED,
        ];
    }

    /**
     * Fetches job applied by company id | Only first result
     * @param Int companyId
     * @return JobApplied|Null
     */
    public function getJobAppliedByCompanyId($companyId = null)
    {
        return $this->listJobApplied(['company_id' => $companyId], true);
    }

    /**
     * Fetches job applied by job applied id | Only first result
     * @param Int jobAppliedId
     * @return JobApplied|Null
     */
    public function getJobAppliedByApplianedId($jobAppliedId = null)
    {
        return $this->listJobApplied(['jobAppliedId' => $jobAppliedId], true);
    }

    /**
     * Fetches all job applied by sent parameters
     * @param Array data - schema: ['professional_id' => Int, 'job_id' => Int, 'company_id' => Int, 'status' => String, 'jobAppliedId' => Int]
     * @param Bool first - only the first result | Default is false
     * @return ArrayOfJobApplied|Null
     */
    public function listJobApplied($data = [], $first = false)
    {
        $queryObj = JobApplied::select('jobs_applieds.*',
            'jobslist.company_id', 'jobslist.job_modality_id', 'jobslist.job_city', 'jobslist.job_country', 'jobslist.job_seniority', 'jobslist.job_salary',
            'jobslist.job_description', 'jobslist.job_experience_description', 'jobslist.experience_in_months', 'jobslist.job_benefits',
            'companies.company_slug', 'companies.company_register_number', 'companies.company_name', 'companies.company_slug', 'companies.company_type'
        )->leftJoin('jobslist', function($join){
            $join->on('jobslist.job_id', '=', 'jobs_applieds.job_id');
        })->leftJoin('companies', function($join){
            $join->on('companies.company_id', '=', 'jobslist.company_id');
        });
        if(array_key_exists('professional_id', $data))
            $queryObj->where('jobs_applieds.professional_id', $data['professional_id']);
        if(array_key_exists('job_id', $data))
            $queryObj->where('jobs_applieds.job_id', $data['job_id']);
        if(array_key_exists('company_id', $data))
            $queryObj->where('jobslist.company_id', $data['company_id']);
        if(array_key_exists('status', $data))
            $queryObj->where('jobs_applieds.status', $data['status']);
        if(array_key_exists('jobAppliedId', $data))
            $queryObj->where('jobs_applieds.applied_id', $data['jobAppliedId']);
        $queryObj->orderBy('jobs_applieds.updated_at', 'DESC');
        return $first ? $queryObj->first() : $queryObj->get();
    }

    /**
     * Sends an email to informed professional
     * @param Array data - schema: ['professioanl_id' => Int, 'applied_id' => Int, 'status' = > String] | All required
     * @return Bool
     */
    public function sendStatusEmail(Array $data)
    {
        if(!array_key_exists('professional_id', $data) || 
           !array_key_exists('applied_id', $data) || 
           !array_key_exists('status', $data) || 
           !in_array($data['status'], $this->getStatus())){
            return false;
        }
        $professional = Professional::find($data['professional_id']);
        $jobApplied = $this->getJobAppliedByApplianedId($data['applied_id'], ['company']);
        if(!$professional || !$jobApplied)
            return false;
        $languageISO = (new Person())->getLanguageIso($professional->person_id);
        $languageISO = !$languageISO ? 'en' : $languageISO;
        $translatedText = (new Translation())->getTranslationsByCategory(Translation::CATEGORY_SYSTEM_TRANSLATIONS, $languageISO);
        $renderedView = view('email_templates.job_application_changed_status', [
            'personName' => $professional->getFullName(),
            'jobName' => $jobApplied->job_description,
            'companyName' => $jobApplied->company_name,
            'applicationStatus' => $data['status'],
            'statusTranslation' => $translatedText[$data['status']],
            'translations' => $translatedText
        ]);
        $emailObj = new \App\Helpers\Mail();
        $resultOfEmailSend = $emailObj->sendMail($professional->professional_email, ucfirst($translatedText['update on your job application']), $renderedView);
        if(!$resultOfEmailSend['success'])
            return false;
        return true;
    }
}