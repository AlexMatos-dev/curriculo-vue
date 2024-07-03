<?php

namespace Database\Seeders;

use App\Models\CommonCurrency;
use App\Models\Company;
use App\Models\CompanyAdmin;
use App\Models\CompanyType;
use App\Models\JobList;
use App\Models\JobModality;
use App\Models\JobSkill;
use App\Models\ListCountry;
use App\Models\ListLangue;
use App\Models\ListProfession;
use App\Models\Person;
use App\Models\Proficiency;
use App\Models\Tag;
use App\Models\TypeVisas;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateFakeJobData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $limit = false;
        while(!is_numeric($limit)){
            $limit = $this->command->ask('Enter the number of JobList records to create');
        }
        if($limit < 1)
            $limit = 1;
        $faker = Faker::create('pt_BR');
        // -- Start of Essential Data --
        $sourceImage = file_exists(storage_path('app/placeholder.png')) ? file_get_contents(storage_path('app/placeholder.png')) : null;
        $countryObj = ListCountry::where('lcountry_acronyn', 'br')->first();
        if(!$countryObj)
            return;
        $jobModalities = JobModality::all()->toArray();
        $seniorities = Proficiency::where('category', Proficiency::CATEGORY_SENIORITY)->get()->toArray();
        $languagesArray = ListLangue::all()->toArray();
        $languageProfeciency = Proficiency::where('category', Proficiency::CATEGORY_LANGUAGE)->get()->toArray();
        $skillsProfeciency = Proficiency::where('category', Proficiency::CATEGORY_LEVEL)->get()->toArray();
        $skills = Tag::all()->toArray();
        $visasArray = TypeVisas::all()->toArray();
        $countriesArray = ListCountry::all()->toArray();
        $professions = ListProfession::all()->toArray();
        $currencies = CommonCurrency::all()->toArray();
        $companyTypes = CompanyType::all()->toArray();
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
        // -- End of Essential Data --
        // Create Companies
        $created = 0;
        for($i = 0; $i < 5; $i++){
            if($created > 5)
                break;
            $person = Person::inRandomOrder()->first();
            if(!$person){
                $person = Person::create([
                    'person_username' => $faker->firstName('masculine') . ' ' . $faker->lastName('masculine'),
                    'person_email' => $faker->email,
                    'person_password' => Hash::make(12345),
                    'person_ddi' => substr($faker->phoneNumber(), 0, 2),
                    'person_phone' => $faker->phoneNumber(),
                    'person_langue' => ListLangue::inRandomOrder()->first()->llangue_id,
                    'last_login' => $faker->dateTime()
                ]);
            }
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
                    'company_type' => $companyTypes[array_rand($companyTypes)]['company_type_id'],
                    'company_logo' => $sourceImage,
                    'company_cover_photo' => $sourceImage,
                    'company_video' => $faker->url('youtube'),
                    'company_email' => $faker->unique()->companyEmail(),
                    'company_phone' => $faker->unique()->phoneNumber(),
                    'company_website' => $faker->url($companyName),
                    'company_description' => $faker->text(499),
                    'company_number_employees' => $faker->randomNumber(2),
                    'company_benefits' => $faker->text(2000),
                    'paying' => $i < 2 ? true : false
                ]);
                CompanyAdmin::create([
                    'company_id' => $company->company_id,
                    'person_id' => $person->person_id,
                    'has_privilegies' => true
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
        $companiesData = Company::inRandomOrder()->get()->toArray();
        for($o = 0; $o < $limit; $o++){
            if($created > $limit)
                break;
            $job = null;
            try {
                $seniorityOfJob = $seniorities[array_rand($seniorities)];
                $jobData = $jobSeniorities[$seniorityOfJob['proficiency_level']];
                $profession = $professions[array_rand($professions)];
                $job = JobList::create([
                    'company_id' => $companiesData[array_rand($companiesData)]['company_id'],
                    'job_modality_id' => $jobModalities[array_rand($jobModalities)]['job_modality_id'],
                    'job_city' => $faker->city(),
                    'job_country' => $countryObj->lcountry_id,
                    'job_seniority' => $seniorityOfJob['proficiency_id'],
                    'job_salary' => $faker->numberBetween($jobData['salary'][0], $jobData['salary'][1]),
                    'job_description' => $profession['profession_name'],
                    'job_experience_description' => $faker->text(80),
                    'experience_in_months' => $jobData['exp'],
                    'job_benefits' => $faker->text(500),
                    'job_offer' => $faker->text(499),
                    'job_requirements' => $faker->text(499),
                    'wage_currency' => $currencies[array_rand($currencies)]['common_currency_id'],
                    'profession_for_job' => $profession['lprofession_id']
                ]);
                if($job){
                    for($in = 0; $in < random_int(2, 10); $in++){
                        $thisSkill = $skills[array_rand($skills)];
                        $thisProficiency = $skillsProfeciency[array_rand($skillsProfeciency)];
                        JobSkill::create([
                            'joblist_id' => $job->job_id,
                            'tag_id' => $thisSkill['tags_id'],
                            'proficiency_id' => $thisProficiency['proficiency_id']
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