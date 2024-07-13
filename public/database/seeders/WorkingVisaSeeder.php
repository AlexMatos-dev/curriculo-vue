<?php

namespace Database\Seeders;

use App\Models\Translation;
use App\Models\WorkingVisa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkingVisaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/workingVisas.json');
        if(!file_exists($path))
            return;
        $workingVisas = json_decode(file_get_contents($path), true);
        $object = new WorkingVisa();
        foreach($workingVisas as $workingVisa){
            if($object::where('name', $workingVisa['en'])->first())
                continue;
            $result = WorkingVisa::create([
                'name' => $workingVisa['en']
            ]);
            if(!$result || Translation::where('en', $workingVisa['en'])->first())
                continue;
            Translation::create([
                'en' => $workingVisa['en'],
                'pt' => $workingVisa['pt'],
                'es' => $workingVisa['es'],
                'category' => Translation::CATEGORY_WORKING_VISA
            ]);
        }
    }
}
