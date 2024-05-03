<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model 
{
    protected $primaryKey = 'country_id';
    protected $table = 'countries';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'curriculum_id',
        'country_name'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_id')->first();
    }

    public function listCountry()
    {
        return $this->belongsTo(ListCountry::class, 'country_name')->first();
    }
    
    /**
     * Tries to find a suitable Country accordingly to parameters, else creates a new Country
     * @param Array valuesArray - Schema ['curriculum_id' => $curriculum_id, 'country_name' => $country_name]
     * @param Country|False
     */
    public function findOrCreateCountry($valuesArray = [])
    {
        $curriculumId = array_key_exists('curriculum_id', $valuesArray) ? $valuesArray['curriculum_id'] : null;
        $countryName = array_key_exists('country_name', $valuesArray)  ? $valuesArray['country_name']  : null;
        if(!$curriculumId || !$countryName)
            return false;
        $country = Country::where('curriculum_id', $curriculumId)->where('country_name', $countryName)->first();
        if($country)
            return $country;
        return Country::create([
            'curriculum_id' => $curriculumId,
            'country_name' => $countryName
        ]);
    }

    /**
     * Tries to remove sent country id if it is not being used anymore
     * @param Integer country_id
     * @param Integer curriculum_id
     * @return Bool
     */
    public function tryToRemove($country_id, $curriculum_id)
    {
        if(Visa::where('vicurriculum_id', $curriculum_id)->where('vicountry_id', $country_id)->first())
            return false;
        return Country::where('country_id', $country_id)->delete();
    }
}