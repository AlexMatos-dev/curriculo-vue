<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model 
{
    protected $primaryKey = 'curriculum_id';
    protected $table = 'curriculums';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cprofes_id',
        'clengua_id'
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'cprofes_id')->first();
    }

    public function listLangue()
    {
        return $this->belongsTo(ListLangue::class, 'clengua_id')->first();
    }
}