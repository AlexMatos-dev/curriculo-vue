<?php

namespace App\Http\Controllers;

use App\Models\ListCountry;

class CountryController extends Controller
{
    public function getCountries()
    {
        $country = ListCountry::leftJoin('translations AS t', function ($join)
        {
            $join->on('listcountries.lcountry_name', '=', 't.en');
        });
        $countries = $country->get();
        if(!request('flag_source')){
            $countriesFiltered = [];
            foreach($countries as $country){
                $country->flag = "{$country->lcountry_acronyn}.svg";
                $countriesFiltered[] = $country;
            }
            $countries = $countriesFiltered;
        }
        returnResponse($countries, 200);
    }
}
