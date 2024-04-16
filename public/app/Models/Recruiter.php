<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recruiter extends Model
{
    protected $table = 'recruiters';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'recruiter_photo',
        'paying'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->first();
    }
}