<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListState extends Model
{
    protected $primaryKey = 'lstate_id';
    protected $table = 'liststates';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lstates_name',
        'lstates_parent_id',
        'lstates_level',
        'lstacountry_id'
    ];

    public function listState()
    {
        return $this->belongsTo(ListState::class, 'lstates_parent_id')->first();
    }

    public function listCountry()
    {
        return $this->belongsTo(ListCountry::class, 'lstacountry_id')->first();
    }
}