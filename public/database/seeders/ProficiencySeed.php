<?php

namespace Database\Seeders;

use App\Models\Proficiency;
use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProficiencySeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/profeciencyLevel.json');
        if(!file_exists($path))
            return;
        $proficiencyData = json_decode(file_get_contents($path), true);
        $profeciencyObj = new Proficiency();
        foreach($proficiencyData as $data){
            if($profeciencyObj::where('proficiency_level', $data['en'])->first())
                continue;
            $result = Proficiency::create([
                'proficiency_level' => $data['en'],
            ]);
            if(!$result || Translation::where('en', $data['en'])->first())
                continue;
            Translation::create([
                'en' => $data['en'],
                'pt' => $data['pt'],
                'es' => $data['es']
            ]);
        }
    }
}
