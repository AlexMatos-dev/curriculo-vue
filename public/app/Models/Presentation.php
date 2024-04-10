<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{
    protected $table = 'presentations';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'precurriculum_id',
        'presentation_text'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'precurriculum_id')->first();
    }
}