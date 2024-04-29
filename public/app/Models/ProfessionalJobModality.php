<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalJobModality extends Model
{
    protected $primaryKey = 'professional_job_modality_id';
    protected $table = 'professionals_job_modalities';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'professional_id',
        'job_modality_id'
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'professional_id')->first();
    }

    public function jobModality()
    {
        return $this->belongsTo(JobModality::class, 'job_modality_id')->first();
    }
}
