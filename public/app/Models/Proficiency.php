<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proficiency extends Model
{
    protected $primaryKey = 'proficiency_id';
    protected $table = 'proficiency';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'proficiency_level'
    ];
}