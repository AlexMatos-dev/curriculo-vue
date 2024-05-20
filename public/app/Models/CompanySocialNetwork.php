<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySocialNetwork extends Model
{
    protected $primaryKey = 'social_network_id';
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
        'social_network_type_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function socialNetworkType()
    {
        return $this->belongsTo(CompanySocialNetworkType::class, 'social_network_type_id');
    }
}
