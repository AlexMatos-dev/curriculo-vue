<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{
    protected $primaryKey = 'presentation_id';
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