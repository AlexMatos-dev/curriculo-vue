<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplied extends Model
{
    protected $table = 'jobs_applieds';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
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
}