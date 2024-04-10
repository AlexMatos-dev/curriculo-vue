<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model 
{
    protected $table = 'experiences';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'excurriculum_id',
        'exjob_title',
        'excompany_name',
        'exstart_date',
        'exend_date',
        'exdescription'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'excurriculum_id')->first();
    }
}