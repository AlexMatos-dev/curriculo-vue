<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    protected $table = 'references';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'refcurriculum_id',
        'reference_name',
        'reference_email',
        'refrelationship'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'refcurriculum_id');
    }
}