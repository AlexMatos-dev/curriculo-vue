<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalProfession extends Model
{
    protected $primaryKey = 'professional_profession_id';
    protected $table = 'professionals_professions';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'professional_id',
        'lprofession_id',
        'started_working_at',
        'observations'
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'professional_id')->first();
    }

    public function profession()
    {
        return $this->belongsTo(ListProfession::class, 'lprofession_id')->first();
    }
}
