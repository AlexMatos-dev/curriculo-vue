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
        $flagPath = storage_path('app/dbSourceFiles/flags');
        foreach($countryArray as $isoCode => $data){
            if($countryObj::where('lcountry_name', $data['en'])->first())
                continue;
            $flagSource = null;
            if(file_exists($flagPath . "/$isoCode.svg"))
                $flagSource = file_get_contents($flagPath . "/$isoCode.svg");
            $spokenLanguages = array_key_exists('spokenLanguages', $data) ? $data['spokenLanguages'] : [];
            $result = ListCountry::create([
                'lcountry_name' => $data['en'],
                'lcountry_acronyn' => $isoCode,
                'ddi' => $data['ddi'],
                'flag' => $flagSource,
                'spokenLanguages' => json_encode($spokenLanguages)
            ]);
            if(!$result || Translation::where('en', $data['en'])->first())
                continue;
            $unoficialTranslation = [];
            foreach($data['translations'] as $langIso => $trans){
                if($trans)
                    $unoficialTranslation[$langIso] = $trans;
            }
            $unoficialTranslation = empty($unoficialTranslation) ? null : json_encode($unoficialTranslation);
            Translation::create([
                'en' => $data['en'],
                'pt' => $data['pt'],
                'es' => $data['es'],
                'category' => Translation::CATEGORY_COUNTRY,
                'unofficial_translations' => $unoficialTranslation
            ]);
        }
    }
}
