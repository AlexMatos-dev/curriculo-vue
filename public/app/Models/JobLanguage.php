<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobLanguage extends Model
{
    protected $primaryKey = 'job_language_id';
    protected $table = 'job_languages';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'joblist_id',
        'language_id',
        'proficiency_id'
    ];

    public function joblist()
    {
        return $this->belongsTo(JobList::class, 'joblist_id')->first();
    }

    public function language()
    {
        return $this->belongsTo(ListLangue::class, 'language_id')->first();
    }

    public function proficiency()
    {
        return $this->belongsTo(Proficiency::class, 'proficiency_id')->first();
    }
}
