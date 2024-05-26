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
        $jobModalities = [
            'remote' => [
                'namePt' => 'remoto', 'nameEs' => 'remota', 'description' => 'distance work, outside the office or company', 
                'ptDescription' => 'trabalho a distancia, fora do escritório ou empresa', 'esDescription' => 'trabajar de forma remota, fuera de la oficina o empresa'
            ],
            'hybrid' => ['namePt' => 'hibrido', 'nameEs' => 'híbrido', 'description' => 'work part of the time remotely and the other part in person',
                'ptDescription' => 'trabalhar parte do tempo remotamente e outra parte pessoalmente', 'esDescription' => 'trabajar parte del tiempo de forma remota y la otra parte en persona'
            ],
            'on-site' => ['namePt' => 'presencial', 'nameEs' => 'presencial', 'description' => 'face-to-face work, within the office or company',
                'ptDescription' => 'trabalho presencial, dentro do escritório ou empresa', 'esDescription' => 'trabajo presencial, dentro de la oficina o empresa'
            ]
        ];
        $jobModality = new JobModality();
        foreach($jobModalities as $en => $data){
            if($jobModality::where('name', $en)->first())
                continue;
            $result = JobModality::create([
                'name' => $en,
                'description' => $data['description']
            ]);
            if(!$result)
                continue;
            $enTranslation = Translation::where('en', $en)->first();
            if(!$enTranslation){
                Translation::create([
                    'en' => $en,
                    'pt' => $data['namePt'],
                    'es' => $data['nameEs'],
                    'category' => Translation::CATEGORY_JOB_MODALITY
                ]);
            }

            $descriptionTranslation = Translation::where('en', $data['description'])->first();
            if(!$descriptionTranslation){
                Translation::create([
                    'en' => $data['description'],
                    'pt' => $data['ptDescription'],
                    'es' => $data['esDescription'],
                    'category' => Translation::CATEGORY_JOB_MODALITY
                ]);
            }
        }
    }
}
