<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySocialNetwork extends Model 
{
    protected $table = 'companies_social_networks';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'social_network_profile',
        'company_id',
        'social_network_type'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->first();
    }

}