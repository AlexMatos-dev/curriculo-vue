<?php

namespace Database\Seeders;

use App\Models\ListLangue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/languages.json');
        if(!file_exists($path))
            return;
        $languageArray = json_decode(file_get_contents($path), true);
        $langObj = new ListLangue();
        foreach($languageArray as $language){
            $langName = $language['ptLang'];
            if($langObj::where('llangue_name', $langName)->first())
                continue;
            ListLangue::create([
                'llangue_name' => $langName
            ]);
        }
    }
}
