<?php

namespace App\Models;

use App\Helpers\TranslatorHandler;
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
        $textToTranslate = [
            'Your change password code:',
            'The code will expire in 30 minutes and can be used only once.',
            'In case you did not request this email, please ignore.',
            'Thanks.',
            'Team'
        ];
        try {
            $languageISO = $this->language()->getIsoCode();
        } catch (\Throwable $th) {
            $languageISO = 'pt';
        }
        $translatedText = TranslatorHandler::translateAll($textToTranslate, $languageISO);
        foreach($textToTranslate as $text){
            if(!array_key_exists($text, $translatedText))
                $translatedText[$text][$languageISO] = $text;
        }
        $code = strtoupper($this->generatePasswordCode());
        $renderedEmail = view('email_templates/password_reset_code', [
            'code' => $code,
            'translations'=> $translatedText,
            'languageIso' => $languageISO
        ]);
        $emailObj = new \App\Helpers\Mail();
        $resultOfEmailSend = $emailObj->sendMail(auth('api')->user()->person_email, TranslatorHandler::translate('Your password change code!', $languageISO), $renderedEmail);
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
}