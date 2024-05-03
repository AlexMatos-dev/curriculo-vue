<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyAdmin extends Model
{
    protected $primaryKey = 'company_admin_id';
    protected $table = 'companies_admins';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'person_id',
        'has_privilegies'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->first();
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id')->first();
    }
}
