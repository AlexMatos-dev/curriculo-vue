<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListCity extends Model
{
    protected $primaryKey = 'lcity_id';
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

    public function getCountry()
    {
        return ListCity::where('lcity_id', $this->lcity_id)->leftJoin('liststates AS state', function($join){
            $join->on('listcities.lcitstates_id', '=', 'state.lstates_id');
        })->leftJoin('listcountries AS country', function($join){
            $join->on('state.lstacountry_id', '=', 'country.lcountry_id');
        })->first();
    }
}