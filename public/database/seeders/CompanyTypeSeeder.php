<?php

namespace Database\Seeders;

use App\Models\CompanyType;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class CompanyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/companyTypes.json');
        if (!file_exists($path))
            return;
        $companyTypes = json_decode(file_get_contents($path), true);
        foreach ($companyTypes as $translatedName){
            if(CompanyType::where('name', $translatedName['en'])->first())
                continue;
            CompanyType::create([
                'name' => $translatedName['en']
            ]);
            Translation::create([
                'en' => $translatedName['en'],
                'pt' => $translatedName['pt'],
                'es' => $translatedName['es'],
                'category' => Translation::CATEGORY_COMPANY_TYPE
            ]);
        }
    }
}
