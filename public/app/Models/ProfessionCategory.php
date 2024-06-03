<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionCategory extends Model
{
    protected $primaryKey = 'profession_category_id';
    protected $table = 'profession_categories';
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
