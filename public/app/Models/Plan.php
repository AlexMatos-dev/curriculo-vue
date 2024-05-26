<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $primaryKey = 'plan_id';
    protected $table = 'plans';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'plan_type',
        'plan_name',
        'plan_price',
        'plan_days_period',
        'plan_months_period'
    ];

    /**
     * Checks sent profile type object and validates whether it can or can't send emails
     * @param Object object
     * @param String objectType
     */
    public function canSendEmails(Object $object, String $objectType)
    {
        // Not implemented nor defined
        return true;
    }
}