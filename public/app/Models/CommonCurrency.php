<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonCurrency extends Model
{
    protected $primaryKey = 'common_currency_id';
    protected $table = 'common_currencies';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'currency',
        'currency_symbol',
        'currency_name'
    ];
}
