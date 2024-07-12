<?php

namespace Database\Seeders;

use App\Models\JobPeriod;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class JobPeriodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/jobPeriods.json');
        if(!file_exists($path))
            return;
        $jobPeriods = json_decode(file_get_contents($path), true);
        $object = new JobPeriod();
        foreach($jobPeriods as $jobPeriod){
            if($object::where('name', $jobPeriod['en'])->first())
                continue;
            $result = JobPeriod::create([
                'name' => $jobPeriod['en']
            ]);
            if(!$result || Translation::where('en', $jobPeriod['en'])->first())
                continue;
            Translation::create([
                'en' => $jobPeriod['en'],
                'pt' => $jobPeriod['pt'],
                'es' => $jobPeriod['es'],
                'category' => Translation::CATEGORY_JOB_PERIOD
            ]);
        }
    }
}
