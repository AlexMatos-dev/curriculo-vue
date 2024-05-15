<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobVisa extends Model
{
    protected $primaryKey = 'job_visa_id';
    protected $table = 'job_visas';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'joblist_id',
        'visas_type_id',
        'country_id'
    ];

    public function joblist()
    {
        return $this->belongsTo(JobList::class, 'joblist_id')->first();
    }

    public function visaType()
    {
        return $this->belongsTo(TypeVisas::class, 'visa_type_id')->first();
    }

    public function country()
    {
        return $this->belongsTo(ListCountry::class, 'country_id')->first();
    }
}
