<?php

namespace Database\Seeders;

use App\Models\ListCountry;
use App\Models\Translation;
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
        foreach($countryArray as $isoCode => $data){
            if($countryObj::where('lcountry_name', $data['en'])->first())
                continue;
            $result = ListCountry::create([
                'lcountry_name' => $data['en'],
                'lcountry_acronyn' => $isoCode
            ]);
            if(!$result || Translation::where('en', $data['en'])->first())
                continue;
            Translation::create([
                'en' => $data['en'],
                'pt' => $data['pt'],
                'es' => $data['es']
            ]);
        }
    }
}
