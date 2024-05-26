<?php

namespace Database\Seeders;

use App\Models\ListProfession;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class ListProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/professions.json');
        if(!file_exists($path))
            return;
        $professionsArray = json_decode(file_get_contents($path), true);
        $lListProfessionObj = new ListProfession();
        foreach($professionsArray as $language){
            if($lListProfessionObj::where('profession_name', $language['en'])->first())
                continue;
            $result = ListProfession::create([
                'profession_name' => $language['en']
            ]);
            if(!$result || Translation::where('en', $language['en'])->first())
                continue;
            Translation::create([
                'en' => $language['en'],
                'pt' => $language['pt'],
                'es' => $language['es'],
                'category' => Translation::CATEGORY_PROFESSION
            ]);
        }
    }
}
