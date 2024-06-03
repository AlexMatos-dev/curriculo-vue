<?php

namespace Database\Seeders;

use App\Helpers\ModelUtils;
use App\Models\ListProfession;
use App\Models\ProfessionCategory;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class ListProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $path = storage_path('app/dbSourceFiles/professions.json');
        $path = storage_path('app/dbSourceFiles/professionsByCategories.json');
        if(!file_exists($path))
            return;
        $professionsArray = json_decode(file_get_contents($path), true);
        $professionCategories = ModelUtils::getAllAsIndexedArray(new ProfessionCategory(), 'name');
        $listProfessionObj = new ListProfession();
        foreach($professionsArray as $category => $professions){
            $categoryObj = $professionCategories[$category];
            foreach($professions as $professionTranslations){
                if($listProfessionObj::where('profession_name', $professionTranslations['en'])->first())
                    continue;
                $result = ListProfession::create([
                    'profession_name' => $professionTranslations['en'],
                    'profession_category_id' => $categoryObj->profession_category_id
                ]);
                if(!$result || Translation::where('en', $professionTranslations['en'])->first())
                    continue;
                Translation::create([
                    'en' => $professionTranslations['en'],
                    'pt' => $professionTranslations['pt'],
                    'es' => $professionTranslations['es'],
                    'category' => Translation::CATEGORY_PROFESSION
                ]);
            }
        }
    }
}
