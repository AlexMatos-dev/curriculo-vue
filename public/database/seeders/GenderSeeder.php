<?php

namespace Database\Seeders;

use App\Models\Gender;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genderArray = [
            "masculine",
            "feminine",
            "other",
        ];
        $genderObj = new Gender();
        foreach($genderArray as $language){
            $langName = mb_strtolower($language);
            if($genderObj::where('gender_name', $langName)->first())
                continue;
            Gender::create([
                'gender_name' => $language
            ]);
        }
    }
}
