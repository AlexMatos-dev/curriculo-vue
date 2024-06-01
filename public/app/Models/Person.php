<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Person extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    const PROFESSIONAL_PERSON_ACCOUNT = 'professional';
    const RECRUITER_PERSON_ACCOUNT    = 'recruiter';
    const COMPANY_PERSON_ACCOUNT      = 'company';

    protected $primaryKey = 'person_id';
    protected $table = 'persons';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'person_username',
        'person_email',
        'person_password',
        'person_ddi',
        'person_phone',
        'person_langue',
        'last_login'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'person_password'
    ];

    public function language()
    {
        return $this->belongsTo(ListLangue::class, 'person_langue')->first();
    }

    /**
     * Send email to $this Person with generated code
     * @return Bool
     */
    public function sendRequestChangePasswordCodeEmail()
    {
        $languageISO = $this->language()->llangue_acronyn;
        $translatedText = (new Translation())->getTranslationsByCategory(Translation::CATEGORY_SYSTEM_TRANSLATIONS, $languageISO);
        $code = strtoupper($this->generatePasswordCode());
        $renderedEmail = view('email_templates/password_reset_code', [
            'code' => $code,
            'translations'=> $translatedText,
            'languageIso' => $languageISO
        ]);
        $emailObj = new \App\Helpers\Mail();
        $resultOfEmailSend = $emailObj->sendMail($this->person_email, $translatedText['Your password change code!'], $renderedEmail);
        if(!$resultOfEmailSend['success'])
            return false;
        Cache::put("resetPasswordCode--{$this->person_id}", "$code", 1800);
        return true;
    }

    /**
     * Generates a random 6 characters (number and letter) password code
     * @return String
     */
    public function generatePasswordCode()
    {
        return Str::random(6);
    }

    /**
     * Gets the object of a Person profile target object.
     * @param String profileType (Company, Professional or Recruiter)
     * @param Int personId
     * @return Object|Null
     */
    public function getProfile($profileType = '')
    {
        switch($profileType){
            case Profile::PROFESSIONAL:
                $object = Professional::join('profiles', function (JoinClause $join) {
                    $join->on('professionals.professional_id', '=', 'profiles.profile_type_id')
                        ->where('profiles.profile_type', '=', Profile::PROFESSIONAL)
                        ->where('profiles.person_id', '=', $this->person_id);
                })->first();
            break;
            case Profile::RECRUITER:
                $object = Recruiter::join('profiles', function (JoinClause $join) {
                    $join->on('recruiters.recruiter_id', '=', 'profiles.profile_type_id')
                        ->where('profiles.profile_type', '=', Profile::RECRUITER)
                        ->where('profiles.person_id', '=', $this->person_id);
                })->first();
            break;
            case Profile::COMPANY:
                $object = Company::join('profiles', function (JoinClause $join) {
                    $join->on('companies.company_id', '=', 'profiles.profile_type_id')
                        ->where('profiles.profile_type', '=', Profile::COMPANY)
                        ->where('profiles.person_id', '=', $this->person_id);
                })->first();
            break;
            default:
                $object = null;
            break;
        }
        return $object;
    }

    /**
     * Returns a slug with sent parameters
     * @param String firstValue
     * @param String lastValue
     * @return String
     */
    public function makeSlug($firstValue = '', $lastValue = '')
    {
        $uuid = str_replace(['.', '/', ','], '', microtime(true));
        return Str::slug("$firstValue $lastValue $uuid");
    }

    /**
     * Returns person language iso by person id
     * @param Int personId
     * @return String|False
     */
    public function getLanguageIso($personId = null)
    {
        $person = Person::leftJoin('listlangues', function($join){
            $join->on('persons.person_langue', '=', 'listlangues.llangue_id');
        })->where('persons.person_id', $personId)->first();
        if(!$person)
            return false;
        return $person->llangue_acronyn;
    }
}