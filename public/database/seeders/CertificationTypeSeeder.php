<?php

namespace Database\Seeders;

use App\Models\CertificationType;
use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CertificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/certificationTypes.json');
        if(!file_exists($path))
            return;
        $certificationsTypeArray = json_decode(file_get_contents($path), true);
        $obj = new CertificationType();
        foreach($certificationsTypeArray as $certificationType){
            if($obj::where('name', $certificationType['en'])->first())
                continue;
            $result = CertificationType::create([
                'name' => $certificationType['en']
            ]);
            if(!$result || Translation::where('en', $certificationType['en'])->first())
                continue;
            Translation::create([
                'en' => $certificationType['en'],
                'pt' => $certificationType['pt'],
                'es' => $certificationType['es'],
                'category' => Translation::CATEGORY_CERTIFICATION_TYPE
            ]);
        }
    }
}
