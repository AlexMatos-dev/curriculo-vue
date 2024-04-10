<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeVisas extends Model
{
    protected $table = 'type_visas';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type_name'
    ];
}