<?php

namespace Database\Seeders;

use App\Models\ProfessionCategory;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class ProfessionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/professionTypes.json');
        if(!file_exists($path))
            return;
        $professionCategoryArray = json_decode(file_get_contents($path), true);
        $professionCategory = new ProfessionCategory();
        foreach($professionCategoryArray as $translations){
            if($professionCategory::where('name', $translations['en'])->first())
                continue;
            $result = ProfessionCategory::create([
                'name' => $translations['en']
            ]);
            if(!$result || Translation::where('en', $translations['en'])->first())
                continue;
            Translation::create([
                'en' => $translations['en'],
                'pt' => $translations['pt'],
                'es' => $translations['es'],
                'category' => Translation::CATEGORY_PROFESSION_TYPE
            ]);
        }
    }
}
