<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListRole extends Model
{
    protected $table = 'list_roles';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lroles_name',
        'lroles_permissions'
    ];
}