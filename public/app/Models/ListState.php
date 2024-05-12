<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;

class ListState extends Model
{
    protected $primaryKey = 'lstates_id';
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
        'lstacountry_id',
        'lstate_acronyn'
    ];

    public function listState()
    {
        return $this->belongsTo(ListState::class, 'lstates_parent_id')->first();
    }

    public function listCountry()
    {
        return $this->belongsTo(ListCountry::class, 'lstacountry_id')->first();
    }

    /**
     * Returns an array containing all ListState belonging to a country
     * @param String countryAcronyn
     * @return Array of ListState
     */
    public function getStatesByCountryAcronyn($countryAcronyn = null)
    {
        $this->countryAcronyn = $countryAcronyn;
        return ListState::join('listcountries', function (JoinClause $join) {
            $join->on('listcountries.lcountry_id', '=', 'liststates.lstacountry_id')
                ->where('listcountries.lcountry_acronyn', '=', $this->countryAcronyn);
        })->get();
    }
}