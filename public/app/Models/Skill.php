<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $primaryKey = 'skill_id';
    protected $table = 'skills';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'skcurriculum_id',
        'skill_name',
        'skproficiency_level',
        'experience_level'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'skcurriculum_id')->first();
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'skill_name')->first();
    }

    public function profeciency()
    {
        return $this->belongsTo(Proficiency::class, 'skproficiency_level')->first();
    }
}