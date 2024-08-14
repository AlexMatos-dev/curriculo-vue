<?php

namespace App\Models;

use App\Helpers\LaravelMail;
use App\Helpers\ModelUtils;
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
     * @param Int jobAppliedId
     * @return JobApplied|Null
     */
    public function getJobAppliedByCompanyId($companyId = null, $jobAppliedId = null)
    {
        $param = ['company_id' => $companyId];
        if($jobAppliedId)
            $param['jobAppliedId'] = $jobAppliedId;
        return $this->listJobApplied($param, true);
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
     * Fetches job applied by job id | Only first result
     * @param Int jobId
     * @return JobApplied|Null
     */
    public function getJobAppliedByJobId($jobId = null)
    {
        return $this->listJobApplied(['job_id' => $jobId], true);
    }

    /**
     * Checks if job applied is from sent professional id
     * @param Int professional_id
     * @return Bool
     */
    public function isFromPorfessional($professional_id = null)
    {
        return $this->professional_id == $professional_id ? true : false;
    }

    /**
     * Fetches all job applied by sent parameters
     * @param Array data - schema: ['professional_id' => Int, 'job_id' => Int, 'company_id' => Int, 'status' => String, 'jobAppliedId' => Int]
     * @param Bool first - only the first result | Default is false
     * @param Bool onlyJobApplied - default = false (return only a JobApplied instances array)
     * @return ArrayOfJobApplied|Null
     */
    public function listJobApplied($data = [], $first = false, $onlyJobApplied = false)
    {
        if(!$onlyJobApplied){
            $queryObj = JobApplied::select('jobs_applieds.*',
                'jobslist.company_id', 'jobslist.job_modality_id', 'jobslist.job_city', 'jobslist.job_country', 'jobslist.job_seniority', 'jobslist.job_title',
                'jobslist.job_description', 'jobslist.job_experience_description', 'jobslist.experience_in_months', 'jobslist.job_benefits',
                'companies.company_slug', 'companies.company_register_number', 'companies.company_name', 'companies.company_slug', 'jobslist.contact_name',
                'jobslist.contact_email', 'jobslist.contact_phone', 'jobslist.contact_website', 'companies.paying AS paying_company'
            )->leftJoin('jobslist', function($join){
                $join->on('jobslist.job_id', '=', 'jobs_applieds.job_id');
            })->leftJoin('companies', function($join){
                $join->on('companies.company_id', '=', 'jobslist.company_id');
            });
        }else{
            $queryObj = JobApplied::select('jobs_applieds.*');
        }
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
        $results = $first ? $queryObj->first() : $queryObj->get();
        $languageIso = Session()->get('user_lang');
        $countries = ModelUtils::getTranslationsArray(new \App\Models\ListCountry(), 'lcountry_name', [], 'lcountry_id');
        $formatedData = [];
        foreach($results as $jobApplied){
            if($jobApplied->paying_company)
                continue;
            $data = $jobApplied;
            $country = '';
            if(array_key_exists($jobApplied->job_country, $countries))
                $country = ucfirst($countries[$jobApplied->job_country][$languageIso]);
            $data->countryName = $country;
            $data->company_name = ucfirst($jobApplied->company_name);
            $data->job_title = ucfirst($jobApplied->job_title);
            $data->statusTranslation = $jobApplied->getStatusTranslation();
            $data->applicationDate = ModelUtils::parseDateByLanguage($jobApplied->created_at, false, $languageIso);
            $data->lastUpdateDate = ModelUtils::parseDateByLanguage($jobApplied->updated_at, false, $languageIso);
            $formatedData[] = $data;
        }
        return $formatedData;
    }

    /**
     * Sends an email to informed professional
     * @param Array data - schema: ['professional_id' => Int, 'applied_id' => Int, 'status' = > String] | All required
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
        $languageISO = Session()->get('user_lang') ? Session()->get('user_lang') : (new Person())->getLanguageIso($this->person_id);
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
        $mail = new LaravelMail($professional->professional_email, ucfirst($translatedText['update on your job application']), $renderedView);
        $resultOfEmailSend = $mail->sendMail();
        if(!$resultOfEmailSend['success'])
            return false;
        return true;
    }

    /**
     * Returns the translation of the appliance status
     * @return String
     */
    public function getStatusTranslation()
    {
        switch($this->status){
            case $this::STATUS_APPLIED:
                return ucfirst(translate('applied'));
            break;
            case $this::STATUS_VALIDATION:
                return ucfirst(translate('validating'));
            break;
            case $this::STATUS_REFUSED:
                return ucfirst(translate('refused'));
            break;
            case $this::STATUS_ACCEPTED:
                return ucfirst(translate('accepted'));
            break;
        }
        return '';
    }
}