<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificationType extends Model
{
    protected $primaryKey = 'certification_type';
    protected $table = 'certification_types';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lcountry'
    ];

    public function country()
    {
        return $this->belongsTo(ListCountry::class, 'lcountry_id')->first();
    }
}
