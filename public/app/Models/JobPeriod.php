<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPeriod extends Model
{
    protected $primaryKey = 'job_period';
    protected $table = 'job_periods';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];
}
