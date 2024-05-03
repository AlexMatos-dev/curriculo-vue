<?php

namespace Database\Seeders;

use App\Models\Translation;
use App\Models\TypeVisas;
use Illuminate\Database\Seeder;

class VisasTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/visas.json');
        if(!$path)
            return;
        $visaTypes = json_decode(file_get_contents($path), true);
        $visaTypeObj = new TypeVisas();
        foreach($visaTypes as $visaType){
            if($visaTypeObj::where('type_name', $visaType['en'])->first())
                continue;
            $result = TypeVisas::create([
                'type_name' => $visaType['en']
            ]);
            if(!$result || Translation::where('en', $visaType['en'])->first())
                continue;
            Translation::create([
                'en' => $visaType['en'],
                'pt' => $visaType['pt'],
                'es' => $visaType['es']
            ]);
        }
    }
}
