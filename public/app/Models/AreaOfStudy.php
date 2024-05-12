<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaOfStudy extends Model
{
    protected $primaryKey = 'area_of_study_id';
    protected $table = 'areas_of_study';
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
