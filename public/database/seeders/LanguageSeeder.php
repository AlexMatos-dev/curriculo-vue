<?php

namespace Database\Seeders;

use App\Models\ListLangue;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/languages.json');
        if(!file_exists($path))
            return;
        $languageArray = json_decode(file_get_contents($path), true);
        $langObj = new ListLangue();
        foreach($languageArray as $iso => $language){
            if($langObj::where('llangue_name', $language['en'])->first())
                continue;
            $result = ListLangue::create([
                'llangue_name' => $language['en'],
                'llangue_acronyn' => $iso
            ]);
            if(!$result || Translation::where('en', $language['en'])->first())
                continue;
            Translation::create([
                'en' => $language['en'],
                'pt' => $language['pt'],
                'es' => $language['es']
            ]);
        }
    }
}
