<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobSkill extends Model
{
    protected $primaryKey = 'job_skill_id';
    protected $table = 'job_skills';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'joblist_id',
        'tag_id',
        'proficiency_id'
    ];

    public function joblist()
    {
        return $this->belongsTo(JobList::class, 'joblist_id')->first();
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag_id')->first();
    }

    public function proficiency()
    {
        return $this->belongsTo(Proficiency::class, 'proficiency_id')->first();
    }
}
