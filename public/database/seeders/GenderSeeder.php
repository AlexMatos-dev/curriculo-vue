<?php

namespace Database\Seeders;

use App\Models\Gender;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genderArray = [
            ['en' => 'masculine', 'pt' => 'masculino', 'es' => 'masculino'],
            ['en' => 'feminine', 'pt' => 'feminino', 'es' => 'femenino'],
            ['en' => 'other', 'pt' => 'outro', 'es' => 'otro'],
        ];
        $genderObj = new Gender();
        foreach($genderArray as $gender){
            if($genderObj::where('gender_name', $gender['en'])->first())
                continue;
            $result = Gender::create([
                'gender_name' => $gender['en']
            ]);
            if(!$result || Translation::where('en', $gender['en'])->first())
                continue;
            Translation::create([
                'en' => $gender['en'],
                'pt' => $gender['pt'],
                'es' => $gender['es'],
                'category' => Translation::CATEGORY_GENDER
            ]);
        }
    }
}
