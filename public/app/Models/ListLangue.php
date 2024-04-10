<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListLangue extends Model
{
    protected $table = 'listlangues';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'llangue_name'
    ];
}