<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'educations';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'edcurriculum_id',
        'eddegree',
        'edfield_of_study',
        'edinstitution',
        'edstart_date',
        'edend_date',
        'eddescription'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'edcurriculum_id')->first();
    }
}