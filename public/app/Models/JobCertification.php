<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCertification extends Model
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
        'joblist_id',
        'certification_type'
    ];

    public function joblist()
    {
        return $this->belongsTo(JobList::class, 'job_id')->first();
    }
    
    public function certificationType()
    {
        return $this->belongsTo(CertificationType::class, 'certification_type')->first();
    }
}
