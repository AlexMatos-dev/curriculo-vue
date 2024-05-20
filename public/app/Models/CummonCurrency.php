<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CummonCurrency extends Model
{
    protected $primaryKey = 'cummon_currency_id';
    protected $table = 'cummon_currency';
    public $timestamps = true;

    use HasFactory;
}
