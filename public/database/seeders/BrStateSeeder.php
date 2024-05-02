<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ListCountry;
use App\Models\ListState;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/brStates.json');
        if(!file_exists($path))
            return;
        $data = json_decode(file_get_contents($path), true);
        $obj = new ListState();
        $countryObj = ListCountry::where('lcountry_acronyn', 'br')->first();
        if(!$countryObj)
            return;
        foreach($data as $stateInfo){
            if($obj::where('lstates_name', $stateInfo['name'])->first())
                continue;
            ListState::create([
                'lstates_name' => $stateInfo['name'],
                'lstate_acronyn' => $stateInfo['isoCode'],
                'lstacountry_id' => $countryObj->lcountry_id
            ]);
        }
    }
}
