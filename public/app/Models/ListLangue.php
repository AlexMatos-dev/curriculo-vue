<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListLangue extends Model
{
    protected $primaryKey = 'llangue_id';
    protected $table = 'listlangues';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'llangue_name',
        'llangue_acronyn'
    ];
}