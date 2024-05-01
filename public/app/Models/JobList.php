<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class JobList extends Model
{
    protected $primaryKey = 'job_id';
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
        'job_city',
        'job_country',
        'job_seniority',
        'job_salary',
        'job_description',
        'job_skills',
        'job_english_level',
        'job_experience',
        'job_benefits'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function city()
    {
        return $this->belongsTo(ListCity::class, 'city_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
