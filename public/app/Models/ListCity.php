<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListCity extends Model
{
    protected $table = 'listcities';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lcity_name',
        'lcitstates_id'
    ];

    public function listState()
    {
        return $this->belongsTo(ListState::class, 'lcitstates_id');
    }
}