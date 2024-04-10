<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscriptions';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'person_id',
        'payment_id',
        'plan_id'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id')->first();
    }

    public function person()
    {
        return $this->belongsTo(Persons::class, 'person_id')->first();
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id')->first();
    }
}