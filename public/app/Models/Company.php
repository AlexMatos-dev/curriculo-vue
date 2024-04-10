<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_slug',
        'company_register_number',
        'company_name',
        'company_type',
        'company_logo',
        'company_cover_photo',
        'company_video',
        'company_email',
        'company_phone',
        'company_website',
        'company_description',
        'company_number_employees',
        'company_benefits'
    ];
}