<?php

namespace App\Models;

use App\Helpers\TranslatorHandler;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;

class Person extends Authenticatable implements JWTSubject
{
    use Notifiable;

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
        'person_langue'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'person_password'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

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
        $emailTranslations = [
            'your change password code',
            'the code will expire in 30 minutes and can be used only once',
            'in case you did not request this email, please ignore',
            'thanks',
            'team'
        ];
        $translationsForEmail = Translation::whereIn('en', $emailTranslations)->get();
        $languageISO = $this->language()->llangue_acronyn;
        $translatedText = [];
        foreach($translationsForEmail as $translation){
            $translatedText[$translation->en] = $translation->getTranslationByIsoCode($languageISO);
        }
        $code = strtoupper($this->generatePasswordCode());
        $renderedEmail = view('email_templates/password_reset_code', [
            'code' => $code,
            'translations'=> $translatedText,
            'languageIso' => $languageISO
        ]);
        $emailObj = new \App\Helpers\Mail();
        $resultOfEmailSend = $emailObj->sendMail($this->person_email, TranslatorHandler::translate('Your password change code!', $languageISO), $renderedEmail);
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
}