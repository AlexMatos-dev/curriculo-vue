<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/systemTranslations.json');
        if(!file_exists($path))
            return;
        $translationsArray = json_decode(file_get_contents($path), true);
        $translationObj = new Translation();
        foreach($translationsArray as $translation){
            if($translationObj->where('en', $translation['en'])->first())
                continue;
            Translation::create([
                'en' => $translation['en'],
                'pt' => $translation['pt'],
                'es' => $translation['es'],
                'category' => Translation::CATEGORY_SYSTEM_TRANSLATIONS,
                'unofficial_translations' => json_encode($translation['unoficialTranslations'])
            ]);
        }
    }
}
