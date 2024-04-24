<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeVisas extends Model
{
    protected $primaryKey = 'typevisas_id';
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