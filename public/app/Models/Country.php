<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model 
{
    protected $table = 'countries';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'curriculum_id',
        'country_name'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_id')->first();
    }

    public function listCountry()
    {
        return $this->belongsTo(ListCountry::class, 'country_name')->first();
    }
}