<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visa extends Model
{
    protected $table = 'visas';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vicurriculum_id',
        'vicountry_id',
        'visa_type'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'vicurriculum_id')->first();
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'vicountry_id')->first();
    }

    public function visaType()
    {
        return $this->belongsTo(TypeVisas::class, 'visa_type')->first();
    }

}