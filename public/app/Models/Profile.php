<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    const PROFESSIONAL = 'professionals';
    const RECRUITER    = 'recruiters';
    const COMPANY      = 'companies';

    protected $primaryKey = 'profile_id';
    protected $table = 'profiles';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'person_id',
        'profile_type_id',
        'profile_type'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id')->first();
    }
}