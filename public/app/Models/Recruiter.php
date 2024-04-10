<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recruiter extends Model
{
    protected $table = 'recruiters';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'recruiter_photo'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->first();
    }
}