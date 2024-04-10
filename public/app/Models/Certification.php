<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $table = 'certifications';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cercurriculum_id',
        'certification_name',
        'cerissuing_organization',
        'cerissue_date',
        'cert_hours',
        'cerdescription',
        'cerlink'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'cercurriculum_id')->first();
    }
}