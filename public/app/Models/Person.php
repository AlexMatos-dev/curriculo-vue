<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

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
        return $this->belongsTo(ListLangue::class, 'llangue_id')->first();
    }
}