<?php

namespace Database\Seeders;

use App\Models\JobPaymentType;
use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobPaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/jobPayingTypes.json');
        if(!file_exists($path))
            return;
        $jobPayments = json_decode(file_get_contents($path), true);
        $jobPayment = new JobPaymentType();
        foreach($jobPayments as $jobP){
            if($jobPayment::where('name', $jobP['en'])->first())
                continue;
            $result = JobPaymentType::create([
                'name' => $jobP['en']
            ]);
            if(!$result || Translation::where('en', $jobP['en'])->first())
                continue;
            Translation::create([
                'en' => $jobP['en'],
                'pt' => $jobP['pt'],
                'es' => $jobP['es'],
                'category' => Translation::CATEGORY_JOB_PAYMENT_TYPE
            ]);
        }
    }
}
