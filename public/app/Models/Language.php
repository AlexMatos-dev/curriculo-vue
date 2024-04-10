<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'languages';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lacurriculum_id',
        'lalangue_id',
        'laspeaking_level',
        'lalistening_level',
        'lawriting_level',
        'lareading_level'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'lacurriculum_id')->first();
    }

    public function listLanguage()
    {
        return $this->belongsTo(ListLangue::class, 'lalangue_id')->first();
    }

    public function speakingLevel()
    {
        return $this->belongsTo(Proficiency::class, 'laspeaking_level ')->first();
    }

    public function listeningLevel()
    {
        return $this->belongsTo(Proficiency::class, 'lalistening_level')->first();
    }

    public function writtingLevel()
    {
        return $this->belongsTo(Proficiency::class, 'lawriting_level')->first();
    }

    public function readingLevel()
    {
        return $this->belongsTo(Proficiency::class, 'lareading_level')->first();
    }
}