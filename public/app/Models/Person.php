<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Person extends Model
{
    use Notifiable;

    protected $primaryKey = 'person_id';
    protected $table = 'persons';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'person_username',
        'person_email',
        'person_password',
        'person_ddi',
        'person_phone',
        'person_langue'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'person_password'
    ];

    public function language()
    {
        return $this->belongsTo(ListLangue::class, 'llangue_id')->first();
    }
}