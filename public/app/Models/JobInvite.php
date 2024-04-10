<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobInvite extends Model
{
    protected $table = 'jobs_invites';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'company_id ',
        'professional_id'
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'professional_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}