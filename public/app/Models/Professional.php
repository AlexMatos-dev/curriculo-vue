<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professional extends Model
{
    protected $primaryKey = 'professional_id';
    protected $table = 'professionals';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'person_id',
        'professional_slug',
        'professional_firstname',
        'professional_lastname',
        'professional_email',
        'professional_phone',
        'professional_photo',
        'professional_cover',
        'professional_title',
        'paying'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id')->first();
    }
}