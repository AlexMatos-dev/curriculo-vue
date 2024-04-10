<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lroles_id',
        'lrperson_id',
        'lrprofes_id',
        'lrcompan_id',
        'lrrecrut_id'
    ];

    public function listRole()
    {
        return $this->belongsTo(ListRole::class, 'lroles_id')->first();
    }

    public function person()
    {
        return $this->belongsTo(Persons::class, 'lrperson_id')->first();
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'lrprofes_id')->first();
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'lrcompan_id')->first();
    }
}