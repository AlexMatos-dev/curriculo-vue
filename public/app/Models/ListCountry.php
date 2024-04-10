<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListCountry extends Model
{
    protected $table = 'listcountries';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lcountry_name'
    ];
}