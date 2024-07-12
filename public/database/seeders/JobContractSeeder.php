<?php

namespace Database\Seeders;

use App\Models\JobContract;
use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/jobContracts.json');
        if(!file_exists($path))
            return;
        $jobContracts = json_decode(file_get_contents($path), true);
        $object = new JobContract();
        foreach($jobContracts as $jobContract){
            if($object::where('name', $jobContract['en'])->first())
                continue;
            $result = JobContract::create([
                'name' => $jobContract['en']
            ]);
            if(!$result || Translation::where('en', $jobContract['en'])->first())
                continue;
            Translation::create([
                'en' => $jobContract['en'],
                'pt' => $jobContract['pt'],
                'es' => $jobContract['es'],
                'category' => Translation::CATEGORY_JOB_CONTRACT
            ]);
        }
    }
}
