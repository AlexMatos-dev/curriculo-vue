<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Professional extends Model
{
    protected $table = 'professionals';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'professional_slug',
        'professional_name',
        'professional_email',
        'professional_phone',
        'professional_photo'
    ];
}