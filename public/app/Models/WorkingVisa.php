<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingVisa extends Model
{
    protected $primaryKey = 'working_visa';
    protected $table = 'working_visas';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];
}
