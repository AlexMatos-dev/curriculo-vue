<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JobList;
use App\Models\ListCity;
use App\Models\ListCountry;
use App\Models\ListState;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class CreateFakeJobData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        // -- Start of Essential Data --
        $countryObj = ListCountry::where('lcountry_acronyn', 'br')->first();
        if(!$countryObj)
            return;
        // Create fake state
        $stateObj = ListState::where('lstates_name', 'estado cwb')->first();
        if(!$stateObj){
            $stateObj = ListState::create([
                'lstates_name' => 'estado cwb',
                'lstates_parent_id' => null,
                'lstates_level' => 1,
                'lstacountry_id' => $countryObj->lcountry_id
            ]);
            if(!$stateObj)
                return;
        }
        // Create fake city
        $cityObj = ListCity::where('lcity_name', 'cwb')->first();
        if(!$cityObj){
            $cityObj = ListCity::create([
                'lcity_name' => 'cwb',
                'lcitstates_id' => $stateObj->lstate_id
            ]);
            if(!$cityObj)
                return;
        }
        // -- End of Essential Data --
        // Create Companies
        $created = 0;
        for($i = 0; $i < 5; $i++){
            if($created > 5)
                break;
            $companyName = $faker->company();
            $companyObj = Company::where('company_name', $companyName)->first();
            if($companyObj)
                continue;
            $company = null;
            try {
                $company = Company::create([
                    'company_slug' => Str::slug($companyName),
                    'company_register_number' => $faker->uuid(),
                    'company_name' => $companyName,
                    'company_type' => 'fake',
                    'company_logo' => $faker->imageUrl(360, 360, 'company logo', true, 'company logo'),
                    'company_cover_photo' => $faker->imageUrl(360, 360, 'company logo', true, 'company logo'),
                    'company_video' => $faker->url('youtube'),
                    'company_email' => $faker->unique()->companyEmail(),
                    'company_phone' => $faker->unique()->phoneNumber(),
                    'company_website' => $faker->url($companyName),
                    'company_description' => $faker->text(499),
                    'company_number_employees' => $faker->randomNumber(2),
                    'company_benefits' => $faker->text(2000),
                    'paying' => $i < 2 ? true : false
                ]);
            } catch (\Throwable $th) {
                
            }
            if($company)
                $created++;
        }        
        // Create jobs
        $created = 0;
        $jobModels = ['presencial','hibrido','remoto'];
        $jobSeniorities = [
            ['name' => 'trainee', 'salary' => [500, 900], 'exp' => 0],
            ['name' => 'estagiário', 'salary' => [1000,1600], 'exp' => 1],
            ['name' => 'junior', 'salary' => [2000,3000], 'exp' => 2],
            ['name' => 'pleno', 'salary' => [4000,7000], 'exp' => 4],
            ['name' => 'senior', 'salary' => [7000,20000], 'exp' => 5],
        ];
        $skills = ['php','java','laravel','sql','angular'];
        $englishLevels = ['iniciante','intermediario','avançado','fluente','nativo'];
        $professions = [
            "Administrador de Rede",
            "Tecnólogo do Produto Sênior",
            "Tecnólogo em Gestão Financeira",
            "Tecnólogo em Telecomunicações",
            "Tecnólogo(a)",
            "Tecnólogo(a) de Processo de Produção",
            "Tecnólogo(a) em Processamento de Dados",
            "Tecnólogo(a) Mecânico",
            "Web Designer",
        ];
        $fakeCompanies = Company::where('company_type', 'fake')->get()->toArray();
        for($o = 0; $o < 30; $o++){
            if($created > 30)
                break;
            $job = null;
            try {
                $jobData = $jobSeniorities[array_rand($jobSeniorities)];
                $job = JobList::create([
                    'company_id' => $fakeCompanies[array_rand($fakeCompanies)]['company_id'],
                    'job_model' => $jobModels[array_rand($jobModels)],
                    'job_city' => $cityObj->lcity_id,
                    'job_country' => $countryObj->lcountry_id,
                    'job_seniority' => $jobData['name'],
                    'job_salary' => $faker->numberBetween($jobData['salary'][0], $jobData['salary'][1]),
                    'job_description' => $professions[array_rand($professions)],
                    'job_skills' => $skills[array_rand($skills)] . ',' . $skills[array_rand($skills)],
                    'job_english_level' => $englishLevels[array_rand($englishLevels)],
                    'job_experience' => $jobData['exp'],
                    'job_benefits' => $faker->text(500)
                ]);
            } catch (\Throwable $th) {
                dd($th->getMessage());
            }
            if($job)
                $created++;
        }
    }
}
