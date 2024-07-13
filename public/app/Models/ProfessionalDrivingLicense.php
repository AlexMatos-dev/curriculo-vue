<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalDrivingLicense extends Model
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
        'professional_id'
    ];
    
    public function drivingLicense()
    {
        return $this->belongsTo(DrivingLicense::class, 'driving_license')->first();
    }
    
    public function country()
    {
        return $this->belongsTo(ListCountry::class, 'lcountry_id')->first();
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'professional_id')->first();
    }
}
