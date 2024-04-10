<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'person_id',
        'person_type',
        'plan_id'
    ];

    public function person()
    {
        return $this->belongsTo(Persons::class, 'person_id')->first();
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id')->first();
    }
}