<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfession extends Model
{
    protected $primaryKey = 'lprofession_id';
    protected $table = 'listprofessions';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profession_name',
        'parent_profession_id',
        'valid_profession',
        'person_id',
        'profession_category_id'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id')->first();
    }
}
