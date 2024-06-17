<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function getCountries()
    {
        $country = Country::leftJoin('translations AS t', function ($join)
        {
            $join->on('countries.country_name', '=', 't.en');
        });

        return response()->json($country->get(), 200);
    }
}
