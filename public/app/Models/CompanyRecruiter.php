<?php

namespace App\Models;

use App\Helpers\Utils;
use Illuminate\Database\Eloquent\Model;

class CompanyRecruiter extends Model
{
    const ACTIVE_RECRUITER   = 'active';
    const INVITED_RECRUITER  = 'invited';
    const REFUSED_INVITATION = 'refused';

    protected $primaryKey = 'company_recruiter_id';
    protected $table = 'companies_recruiters';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'recruiter_id',
        'company_id',
        'status'
    ];

    public function recruiter()
    {
        return $this->belongsTo(Recruiter::class, 'recruiter_id')->first();
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->first();
    }

    /**
     * Sends an email envitation to $this selected recruiter and save it's token ofr futher checks
     * @param Company - required
     * @return Bool
     */
    public function sendInvitation(Company $company = null)
    {
        if(!$company)
            return false;
        $recruiter = $this->recruiter();
        $languageISO = null;
        if(!$recruiter)
            return false;
        $person = $recruiter->person();
        if(!$person)
            return false;
        $languageISO = $person->getLanguageIso($person->person_id);
        $languageISO = !$languageISO || !in_array($languageISO, Translation::OFFICIAL_LANGUAGES) ? 'en' : $languageISO;
        $translatedText = (new Translation())->getTranslationsByCategory(Translation::CATEGORY_SYSTEM_TRANSLATIONS, $languageISO);
        $token = Utils::generateToken();
        $personName  = $person->getPersonName();
        $renderedEmail = view('email_templates/recruiter_invitation', [
            'personName'   => $personName,
            'company'      => $company,
            'translations' => $translatedText,
            'languageIso'  => $languageISO,
            'url'          => env('FRONTEND_URL') . "/recruiter/invitation/$token"
        ]);
        $mailer = new \App\Helpers\LaravelMail($person->person_email, $translatedText['recruiter invitation'], $renderedEmail);
        $resultOfEmailSend = $mailer->sendMail();
        if(!$resultOfEmailSend['success'])
            return false;
        $this->token = $token;
        return $this->save();
    }

    /**
     * List all recruiters of sent company id
     * @param Int companyId - required
     * @param String perStatus
     * @param Int byCompanyRecruiterId
     * @return CompanyRecruiters
     */
    public function listByCompany($companyId = null, $perStatus = null, $byCompanyRecruiterId = null)
    {
        $query = CompanyRecruiter::select(
            'persons.person_username', 'persons.person_email', 'persons.person_ddi', 'persons.person_phone', 'persons.person_langue', 'persons.currency',
            'recruiters.recruiter_photo', 'companies_recruiters.company_recruiter_id', 'companies_recruiters.status', 
            'companies_recruiters.created_at', 'companies_recruiters.updated_at'
        )->leftJoin('recruiters', function($join){
            $join->on('recruiters.recruiter_id', '=', 'companies_recruiters.recruiter_id');
        })->leftJoin('persons', function($join){
            $join->on('persons.person_id', '=', 'recruiters.person_id');
        })->orderBy('companies_recruiters.updated_at', 'DESC');
        if($companyId)
            $query->where('companies_recruiters.company_id', $companyId);
        if($perStatus)
            $query->where('companies_recruiters.status', $perStatus);
        if($byCompanyRecruiterId)
            $query->where('companies_recruiters.recruiter_id', $byCompanyRecruiterId);
        $data = $query->get();
        if($byCompanyRecruiterId && count($data) < 1)
            return null;
        $response = [];
        foreach($data as $recruiter){
            $obj = $recruiter;
            $obj['recruiter_photo'] = $obj->recruiter_photo ? base64_encode($obj->recruiter_photo) : null;
            if($byCompanyRecruiterId)
                return $obj;
            $response[] = $obj;
        }
        return $response;
    }
}
