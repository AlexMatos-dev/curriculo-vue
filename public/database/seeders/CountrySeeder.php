<?php

namespace Database\Seeders;

use App\Models\ListCountry;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/countries.json');
        if(!file_exists($path))
            return;
        $countryArray = json_decode(file_get_contents($path), true);
        $countryObj = new ListCountry();
        foreach($countryArray as $isoCode => $countryName){
            if($countryObj::where('lcountry_name', $countryName)->first())
                continue;
            ListCountry::create([
                'lcountry_name' => $countryName,
                'lcountry_acronyn' => $isoCode
            ]);
        }
    }
}
