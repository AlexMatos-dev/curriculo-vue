<?php

namespace Database\Seeders;

use App\Models\ListProfessional;
use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListProfessionalSeeder extends Seeder
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
        $listProfessionalObj = new ListProfessional();
        foreach($professionsArray as $language){
            if($listProfessionalObj::where('profession_name', $language['en'])->first())
                continue;
            $result = ListProfessional::create([
                'profession_name' => $language['en']
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
