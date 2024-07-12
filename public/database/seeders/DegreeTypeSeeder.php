<?php

namespace Database\Seeders;

use App\Models\DegreeType;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class DegreeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/degreeTypes.json');
        if(!file_exists($path))
            return;
        $degreeTypeArray = json_decode(file_get_contents($path), true);
        $degreeTypeObj = new DegreeType();
        foreach($degreeTypeArray as $degreeType){
            if($degreeTypeObj::where('name', $degreeType['en'])->first())
                continue;
            $result = DegreeType::create([
                'name' => $degreeType['en']
            ]);
            if(!$result || Translation::where('en', $degreeType['en'])->first())
                continue;
            Translation::create([
                'en' => $degreeType['en'],
                'pt' => $degreeType['pt'],
                'es' => $degreeType['es'],
                'category' => Translation::CATEGORY_DEGREE_TYPE
            ]);
        }
    }
}
