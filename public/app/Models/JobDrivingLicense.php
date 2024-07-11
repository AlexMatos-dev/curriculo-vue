<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobDrivingLicense extends Model
{
    protected $primaryKey = 'job_certification';
    protected $table = 'job_certifications';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'driving_license',
        'country',
        'job_id'
    ];
    
    public function drivingLicense()
    {
        return $this->belongsTo(DrivingLicense::class, 'driving_license')->first();
    }
    
    public function country()
    {
        return $this->belongsTo(ListCountry::class, 'lcountry_id')->first();
    }

    public function joblist()
    {
        return $this->belongsTo(JobList::class, 'job_id')->first();
    }
}
