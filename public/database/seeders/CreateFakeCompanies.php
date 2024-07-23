<?php

namespace Database\Seeders;

use App\Models\CompanyAdmin;
use App\Models\Company;
use App\Models\CompanyType;
use App\Models\ListCountry;
use App\Models\ListLangue;
use App\Models\Person;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateFakeCompanies extends Seeder
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
        $logoPlaceholder       = file_exists(storage_path('app/placeholder/logo-icon-jobifull.png'))  ? file_get_contents(storage_path('app/placeholder/logo-icon-jobifull.png'))  : null;
        $coverPhotoPlaceholder = file_exists(storage_path('app/placeholder/jobifull-retangular.png')) ? file_get_contents(storage_path('app/placeholder/jobifull-retangular.png')) : null;
        $logoPlaceholder       = null;
        $coverPhotoPlaceholder = null;
        
        $countryObj = ListCountry::where('lcountry_acronyn', 'br')->first();
        if(!$countryObj)
            return;
        $companyTypes = CompanyType::all()->toArray();
        $created = 0;
        for($i = 0; $i < $limit; $i++){
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
            try {
                $company = Company::create([
                    'company_slug' => Str::slug($companyName),
                    'company_register_number' => $faker->uuid(),
                    'company_name' => $companyName,
                    'company_type' => $companyTypes[array_rand($companyTypes)]['company_type_id'],
                    'company_logo' => $logoPlaceholder,
                    'company_cover_photo' => $coverPhotoPlaceholder,
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
    }
}
