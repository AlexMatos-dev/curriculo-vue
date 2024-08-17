<?php

namespace App\Models;

use App\Helpers\LaravelMail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPassword;
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
        'last_login',
        'accepted_cookies',
        'person_slug'
    ];

     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'person_password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'person_password' => 'hashed',
        ];
    }

    public function getAuthPassword()
    {
        return $this->person_password;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function getEmailForPasswordReset()
    {
        return $this->person_email;
    }

    public function getPersonId()
    {
        return $this->person_id;
    }

    public function getPersonName()
    {
        return $this->person_username;
    }

    public function language()
    {
        return $this->belongsTo(ListLangue::class, 'person_langue')->first();
    }

    /**
     * Send change password code to the email of $this Person with generated code for password change
     * @return Bool
     */
    public function sendRequestChangePasswordCodeEmail()
    {
        $languageISO = Session()->get('user_lang') ? Session()->get('user_lang') : $this->getLanguageIso($this->person_id);
        $languageISO = !$languageISO ? 'en' : $languageISO;
        $translatedText = (new Translation())->getTranslationsByCategory(Translation::CATEGORY_SYSTEM_TRANSLATIONS, $languageISO);
        $code = strtoupper($this->generateCode());
        $renderedEmail = view('email_templates/password_reset_code', [
            'code' => $code,
            'translations'=> $translatedText,
            'languageIso' => $languageISO
        ]);
        $mailer = new \App\Helpers\LaravelMail($this->person_email, $translatedText['Your password change code!'], $renderedEmail);
        $resultOfEmailSend = $mailer->sendMail();
        if(!$resultOfEmailSend['success']){
            Cache::forget("resetPasswordCode--{$this->person_id}");
            Cache::forget("awaiting_changepass-email-receival-{$this->person_id}");
            return false;
        }
        Cache::put("resetPasswordCode--{$this->person_id}", "$code", 1800);
        return true;
    }

    /**
     * Send verify email code to the email of $this Person with generated code for email verification
     * @return Bool
     */
    public function sendEmailVerificationCodeEmail()
    {
        $languageISO = Session()->get('user_lang') ? Session()->get('user_lang') : $this->getLanguageIso($this->person_id);
        $languageISO = !$languageISO ? 'en' : $languageISO;
        $translatedText = (new Translation())->getTranslationsByCategory(Translation::CATEGORY_SYSTEM_TRANSLATIONS, $languageISO);
        $code = strtoupper($this->generateCode());
        $renderedEmail = view('email_templates/email_verification_code', [
            'code' => $code,
            'translations'=> $translatedText,
            'languageIso' => $languageISO
        ]);
        $mailer = new LaravelMail($this->person_email, translate('your email verification code!'), $renderedEmail);
        $resultOfEmailSend = $mailer->sendMail();
        if(!$resultOfEmailSend['success']){
            Cache::forget("verifyEmailCode--{$this->person_id}");
            Cache::forget("awaiting-emailverification-email-receival-{$this->person_id}");
            return false;
        }
        Cache::put("verifyEmailCode--{$this->person_id}", "$code", 1800);
        return true;
    }

    /**
     * Generates a random 6 characters (number and letter) password code
     * @return String
     */
    public function generateCode()
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

    /**
     * Generates a new slug and saves it top $this person
     * @param Bool dontSave - to not save, only set to $this person
     * @return Bool|String
     */
    public function updateSlug($dontSave = false)
    {
        $slug = vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4) ) . "-{$this->person_id}";
        $this->person_slug = $slug;
        if($dontSave)
            return $this->person_slug;
        return $this->save();
    }
}