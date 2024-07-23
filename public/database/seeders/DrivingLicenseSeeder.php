<?php

namespace Database\Seeders;

use App\Models\DrivingLicense;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class DrivingLicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/drivingLicenses.json');
        if(!file_exists($path))
            return;
        $drivingLicenses = json_decode(file_get_contents($path), true);
        $object = new DrivingLicense();
        foreach($drivingLicenses as $drivingLicense){
            if($object::where('name', $drivingLicense['en'])->first())
                continue;
            $result = DrivingLicense::create([
                'name' => $drivingLicense['en'],
                'symbol' => $drivingLicense['symbol'],
                'description' => $drivingLicense['description']['en']
            ]);
            if(!$result)
                continue;
            if(!Translation::where('en', $drivingLicense['en'])->first()){
                Translation::create([
                    'en' => $drivingLicense['en'],
                    'pt' => $drivingLicense['pt'],
                    'es' => $drivingLicense['es'],
                    'category' => Translation::CATEGORY_DRIVING_LICENSE
                ]);
            }
            if(!Translation::where('en', $drivingLicense['description']['en'])->first()){
                Translation::create([
                    'en' => $drivingLicense['description']['en'],
                    'pt' => $drivingLicense['description']['pt'],
                    'es' => $drivingLicense['description']['es'],
                    'category' => Translation::CATEGORY_DRIVING_LICENSE
                ]);
            }
        }
    }
}
