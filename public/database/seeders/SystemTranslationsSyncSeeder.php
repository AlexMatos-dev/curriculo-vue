<?php

namespace Database\Seeders;

use App\Models\ListLangue;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class SystemTranslationsSyncSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages    = ListLangue::all();
        $translations = Translation::all();
        foreach($translations as $translation){
            foreach($languages as $language){
                if(in_array($language->llangue_acronyn, Translation::OFFICIAL_LANGUAGES))
                    continue;
                try {
                    $translation->getTranslationByIsoCode($language->llangue_acronyn);
                } catch (\Throwable $th) {}
            }
        }
    }
}
