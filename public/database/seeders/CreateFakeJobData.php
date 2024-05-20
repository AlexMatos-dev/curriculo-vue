<?php

namespace Database\Seeders;

use App\Models\CommonCurrency;
use App\Models\Company;
use App\Models\JobList;
use App\Models\JobModality;
use App\Models\JobSkill;
use App\Models\ListCity;
use App\Models\ListCountry;
use App\Models\ListLangue;
use App\Models\ListProfession;
use App\Models\ListState;
use App\Models\Proficiency;
use App\Models\Tag;
use App\Models\TypeVisas;
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
        $jobModalities = JobModality::all()->toArray();
        $seniorities = Proficiency::where('category', Proficiency::CATEGORY_SENIORITY)->get()->toArray();
        $languagesArray = ListLangue::all()->toArray();
        $languageProfeciency = Proficiency::where('category', Proficiency::CATEGORY_LANGUAGE)->get()->toArray();
        $skills = Tag::all()->toArray();
        $visasArray = TypeVisas::all()->toArray();
        $countriesArray = ListCountry::all()->toArray();
        $professions = ListProfession::all()->toArray();
        $currencies = CommonCurrency::all()->toArray();
        $dataForJobLanguage = [];
        foreach($languagesArray as $langData){
            for($i = 0; $i < 3; $i++){
                $randomLangProficiency = $languageProfeciency[array_rand($languageProfeciency)];
                $dataForJobLanguage[] = (Object) [
                    'llangue_id' => $langData['llangue_id'],
                    'proficiency_id' => $randomLangProficiency['proficiency_id']
                ];
            }
        }
        $dataForJobVisas = [];
        foreach($visasArray as $visa){
            for($i = 0; $i < 3; $i++){
                $randomCountry = $countriesArray[array_rand($countriesArray)];
                $dataForJobVisas[] = (Object) [
                    'visas_type_id' => $visa['typevisas_id'],
                    'country_id' => $randomCountry['lcountry_id']
                ];
            }
        }

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
                'lcitstates_id' => $stateObj->lstates_id
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
        $jobSeniorities = [
            'trainee' => ['salary' => [500, 900], 'exp' => 0],
            'junior' => ['salary' => [2000,3000], 'exp' => 2],
            'middle' => ['salary' => [4000,7000], 'exp' => 5],
            'senior' => ['salary' => [7000,20000], 'exp' => 10],
        ];
        $fakeCompanies = Company::where('company_type', 'fake')->inRandomOrder()->get()->toArray();
        for($o = 0; $o < 30; $o++){
            if($created > 30)
                break;
            $job = null;
            try {
                $seniorityOfJob = $seniorities[array_rand($seniorities)];
                $jobData = $jobSeniorities[$seniorityOfJob['proficiency_level']];
                $job = JobList::create([
                    'company_id' => $fakeCompanies[array_rand($fakeCompanies)]['company_id'],
                    'job_modality_id' => $jobModalities[array_rand($jobModalities)]['job_modality_id'],
                    'job_city' => $cityObj->lcity_id,
                    'job_country' => $countryObj->lcountry_id,
                    'job_seniority' => $seniorityOfJob['proficiency_id'],
                    'job_salary' => $faker->numberBetween($jobData['salary'][0], $jobData['salary'][1]),
                    'job_description' => $professions[array_rand($professions)]['profession_name'],
                    'job_experience_description' => $faker->text(80),
                    'experience_in_months' => $jobData['exp'],
                    'job_benefits' => $faker->text(500),
                    'wage_currency' => $currencies[array_rand($currencies)]['common_currency_id']
                ]);
                if($job){
                    for($in = 0; $in < random_int(2, 10); $in++){
                        $thisSkill = $skills[array_rand($skills)];
                        JobSkill::create([
                            'joblist_id' => $job->job_id,
                            'tag_id' => $thisSkill['tags_id']
                        ]);
                    }

                    $langData = [];
                    for($i = 0; $i < 3; $i++){
                        $langData[] = $dataForJobLanguage[array_rand($dataForJobLanguage)];   
                    }                   
                    $job->sycnLanguages((Object) $langData);

                    $visaData = [];
                    for($i = 0; $i < 3; $i++){
                        $visaData[] = $dataForJobVisas[array_rand($dataForJobVisas)];   
                    }                   
                    $job->sycnVisas((Object) $visaData);
                }
            } catch (\Throwable $th) {
                echo $th->getMessage();
            }
            if($job)
                $created++;
        }
    }
}
