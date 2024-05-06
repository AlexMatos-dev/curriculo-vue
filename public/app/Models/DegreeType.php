<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DegreeType extends Model
{
    protected $primaryKey = 'degree_type_id';
    protected $table = 'degree_types';
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
