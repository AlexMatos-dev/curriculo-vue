<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPaymentType extends Model
{
    protected $primaryKey = 'job_payment_type';
    protected $table = 'job_payment_types';
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
