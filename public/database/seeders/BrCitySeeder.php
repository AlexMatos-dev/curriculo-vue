<?php

namespace Database\Seeders;

use App\Helpers\DomDocumentHandler;
use App\Models\City;
use App\Models\ListCity;
use App\Models\ListState;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/brCities.json');
        if(!file_exists($path))
            return;
        $data = json_decode(file_get_contents($path), true);
        $obj = new ListCity();
        $stateObj = new ListState();
        $states = $stateObj->getStatesByCountryAcronyn('br');
        $stateArray = [];
        foreach($states as $state){
            $stateArray[$state->lstate_acronyn] = $state;
        }
        foreach($data as $isoCode => $cities){
            if(!array_key_exists($isoCode, $stateArray))
                continue;
            foreach($cities as $city){
                if($obj::where('lcity_name', $city)->first())
                    continue;
                ListCity::create([
                    'lcity_name' => $city,
                    'lcitstates_id' => $stateArray[$isoCode]->lstates_id
                ]);
            }
        }
    }
}
