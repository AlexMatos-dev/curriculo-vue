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
        $degreeTypeArray = [
            ['en' => 'technical', 'pt' => 'técnico', 'es' => 'tecnico'],
            ['en' => 'master', 'pt' => 'mestre', 'es' => 'maestría'],
            ['en' => 'doctoral', 'pt' => 'doutorado', 'es' => 'doctorado'],
            ['en' => 'bachelor', 'pt' => 'bacharel', 'es' => 'licenciatura']
        ];
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
