<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'person_id',
        'payment_method'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id')->first();
    }

    public function person()
    {
        return $this->belongsTo(Persons::class, 'person_id')->first();
    }
}