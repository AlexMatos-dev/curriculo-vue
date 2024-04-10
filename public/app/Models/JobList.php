<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobList extends Model
{
    protected $table = 'jobslist';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'job_model',
        'job_location',
        'job_city',
        'job_seniority',
        'job_salary',
        'job_description',
        'job_english_level',
        'job_experience',
        'job_benefits'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->first();
    }

}