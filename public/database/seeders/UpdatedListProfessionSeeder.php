<?php

namespace Database\Seeders;

use App\Helpers\ModelUtils;
use App\Models\ListProfession;
use App\Models\ProfessionCategory;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class UpdatedListProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/professionsListByCategories.json');
        if(!file_exists($path))
            return;
        $professionsArray = json_decode(file_get_contents($path), true);
        $professionCategories = ModelUtils::getAllAsIndexedArray(new ProfessionCategory(), 'name');
        $listProfessionObj = new ListProfession();
        foreach($professionsArray as $professions){
            $category = array_key_exists('category', $professions) ? $professions['category'] : '';
            if(!array_key_exists($category, $professionCategories))
                continue;
            $categoryObj = $professionCategories[$category];
            $professionTranslations = $professions;
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
