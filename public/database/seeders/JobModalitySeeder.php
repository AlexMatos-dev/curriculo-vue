<?php

namespace Database\Seeders;

use App\Models\JobModality;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class JobModalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/jobModalities.json');
        if(!file_exists($path))
            return;
        $jobModalities = json_decode(file_get_contents($path), true);
        $jobModality = new JobModality();
        foreach($jobModalities as $data){
            if($jobModality::where('name', $data['en'])->first())
                continue;
            $result = JobModality::create([
                'name' => $data['en'],
                'description' => $data['description']['en']
            ]);
            if(!$result)
                continue;
            $enTranslation = Translation::where('en', $data['en'])->first();
            if(!$enTranslation){
                Translation::create([
                    'en' => $data['en'],
                    'pt' => $data['pt'],
                    'es' => $data['es'],
                    'category' => Translation::CATEGORY_JOB_MODALITY
                ]);
            }
            $descriptionTranslation = Translation::where('en', $data['description']['en'])->first();
            if(!$descriptionTranslation){
                Translation::create([
                    'en' => $data['description']['en'],
                    'pt' => $data['description']['pt'],
                    'es' => $data['description']['es'],
                    'category' => Translation::CATEGORY_JOB_MODALITY_DESCRIPTION
                ]);
            }
        }
    }
}
