<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';
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
        return $this->belongsTo(Person::class, 'person_id')->first();
    }
}