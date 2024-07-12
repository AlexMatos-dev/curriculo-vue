<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrivingLicense extends Model
{
    protected $primaryKey = 'driving_license';
    protected $table = 'driving_licenses';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'symbol',
        'description'
    ];
}
