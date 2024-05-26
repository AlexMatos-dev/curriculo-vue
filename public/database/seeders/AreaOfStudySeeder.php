<?php

namespace Database\Seeders;

use App\Models\AreaOfStudy;
use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaOfStudySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/areasOfStudy.json');
        if(!file_exists($path))
            return;
        $areasofStudyArray = json_decode(file_get_contents($path), true);
        $areaOfStudyObj = new AreaOfStudy();
        foreach($areasofStudyArray as $areaOfStudy){
            if($areaOfStudyObj::where('name', $areaOfStudy['en'])->first())
                continue;
            $result = AreaOfStudy::create([
                'name' => $areaOfStudy['en']
            ]);
            if(!$result || Translation::where('en', $areaOfStudy['en'])->first())
                continue;
            Translation::create([
                'en' => $areaOfStudy['en'],
                'pt' => $areaOfStudy['pt'],
                'es' => $areaOfStudy['es'],
                'category' => Translation::CATEGORY_AREA_OF_STUDIES
            ]);
        }
    }
}
