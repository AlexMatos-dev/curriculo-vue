<?php

namespace App\Models;

use App\Helpers\ModelUtils;
use Illuminate\Database\Eloquent\Model;

class ListCountry extends Model
{
    protected $primaryKey = 'lcountry_id';
    protected $table = 'listcountries';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lcountry_name',
        'lcountry_acronyn',
        'ddi',
        'flag',
        'spokenLanguages'
    ];

    /**
     * Gets all countries and its translations
     * @param Bool flagToBase64 - to encode flag as base 64
     * @return ArrayOfCountry Schema: [$id => $countryObj] 
     */
    public function getAll($flagToBase64 = false)
    {
        $results = ModelUtils::getIdIndexedAndTranslated($this, 'lcountry_name');
        $filtered = [];
        foreach($results as $key => $object){
            if($flagToBase64)
                $object->flag = base64_encode($object->flag);
            $filtered[$key] = $object;
        }
        return $filtered;
    }
}