<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySocialNetworkType extends Model
{
    protected $primaryKey = 'social_network_type_id';
    protected $table = 'companies_social_networks_types';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'author_company',
        'validated'
    ];
}
