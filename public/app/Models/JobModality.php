<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobModality extends Model
{
    protected $primaryKey = 'job_modality_id';
    protected $table = 'job_modalities';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description'
    ];
}
