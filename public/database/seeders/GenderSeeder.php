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
            ['en' => 'masculine', 'pt' => 'masculino'],
            ['en' => 'feminine', 'pt' => 'feminino'],
            ['en' => 'other', 'pt' => 'outro'],
        ];
        $genderObj = new Gender();
        foreach($genderArray as $gender){
            $genderName = mb_strtolower($gender['pt']);
            if($genderObj::where('gender_name', $genderName)->first())
                continue;
            Gender::create([
                'gender_name' => $genderName
            ]);
        }
    }
}
