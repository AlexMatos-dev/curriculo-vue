<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JobList;
use App\Models\ListCity;
use App\Models\ListCountry;
use App\Models\ListState;
use Illuminate\Database\Seeder;

class PurgeFakeJobData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = JobList::all();
        foreach($jobs as $job){
            try {
                $job->delete();
            } catch (\Throwable $th) {
                
            }
        }
        $companies = Company::where('company_type', 'fake')->get();
        foreach($companies as $company){
            try {
                $company->delete();
            } catch (\Throwable $th) {

            }
        }
        try {
            ListCity::where('lcity_name', 'cwb')->delete();
            ListState::where('lstates_name', 'estado cwb')->delete();
            ListCountry::where('lcountry_acronyn', 'br')->delete();
        } catch (\Throwable $th) {
            return;
        }
    }
}
