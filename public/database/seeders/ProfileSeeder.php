<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyAdmin;
use App\Models\Person;
use App\Models\Professional;
use App\Models\Profile;
use App\Models\Recruiter;
use Faker\Factory as Faker;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProfileSeeder extends Seeder
{
    private $company_email;
    private $companyPerson;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $defaultPassword = Hash::make('12345');
        $personData = [
            'company' => [
                'person_username' => 'company X',
                'person_email' => 'company@company.com',
                'person_password' => $defaultPassword,
                'company_email' => 'company@company.com'
            ],
            'recruiter' => [
                'person_username' => 'recruiter X',
                'person_email' => 'recruiter@recruiter.com',
                'person_password' => $defaultPassword
            ],
            'professional' => [
                'person_username' => 'professional X',
                'person_email' => 'professional@professional.com',
                'person_password' => $defaultPassword
            ]
        ];
        $dataOfPerson = [
            'person_ddi' => '12345',
            'person_phone' => '45987878787',
            'person_langue' => 1
        ];
        foreach($personData as $type => $data){
            $dataOfPerson = array_merge($dataOfPerson, $personData[$type]);
            $profileType = null;
            $person = Person::where('person_email', $data['person_email'])->first();
            if(!$person){
                try {
                    $person = Person::create([
                        "person_ddi" => $dataOfPerson['person_ddi'],
                        "person_phone" => $dataOfPerson['person_phone'],
                        "person_langue" => $dataOfPerson['person_langue'],
                        "person_username" => $data['person_username'],
                        "person_email" => $data['person_email'],
                        "person_password" => $data['person_password']
                    ]);
                } catch (\Throwable $th) {
                    continue;
                }
            }
            if(!$person)
                continue;
            switch($type){
                case 'company':
                    // Create Companies
                    if(Company::where('company_email', $data['company_email'])->first())
                        return;
                    $companyName = $faker->company();
                    $newObj = Company::create([
                        'company_slug' => Str::slug($companyName),
                        'company_register_number' => $faker->uuid(),
                        'company_name' => $companyName,
                        'company_type' => 'fake',
                        'company_logo' => $faker->imageUrl(360, 360, 'company logo', true, 'company logo'),
                        'company_cover_photo' => $faker->imageUrl(360, 360, 'company logo', true, 'company logo'),
                        'company_video' => $faker->url('youtube'),
                        'company_email' => $data['company_email'],
                        'company_phone' => $faker->unique()->phoneNumber(),
                        'company_website' => $faker->url($companyName),
                        'company_description' => $faker->text(499),
                        'company_number_employees' => $faker->randomNumber(2),
                        'company_benefits' => $faker->text(2000),
                        'paying' => true
                    ]);
                    if(!$newObj){
                        $person->delete();
                        return;
                    }
                    CompanyAdmin::create([
                        'company_id' => $newObj->company_id,
                        'person_id' => $person->person_id
                    ]);
                    $objId = $newObj->company_id;
                    $profileType = Profile::COMPANY;
                break;
                case 'recruiter':
                    // Create recruiter
                    $companyPerson = Person::where('person_email', 'company@company.com')->first();
                    if(!$companyPerson)
                        return;
                    $this->companyPerson = $companyPerson;
                    $this->company_email = $personData['company']['company_email'];
                    $companyAdmin = CompanyAdmin::join('companies', function(JoinClause $join) {
                        $join->on('companies_admins.company_id', '=', 'companies.company_id')
                            ->where('companies_admins.person_id', '=', $this->companyPerson->person_id)
                            ->where('companies.company_email', '=', $this->company_email);
                    })->first();
                    if(!$companyAdmin)
                        return;
                    $newObj = Recruiter::create([
                        'company_id' => $companyAdmin->company_id,
                        'recruiter_photo' => $faker->imageUrl(360, 360, 'recruiter logo', true, 'recruiter logo'),
                        'paying' => true
                    ]);
                    if(!$newObj){
                        $person->delete();
                        return;
                    }
                    $objId = $newObj->recruiter_id;
                    $profileType = Profile::RECRUITER;
                break;
                case 'professional':
                    // Create professional
                    $newObj = Professional::create([
                        'person_id' => $person->person_id,
                        'professional_slug' => '',
                        'professional_firstname' => $faker->firstName('male'),
                        'professional_lastname' => $faker->lastName('male'),
                        'professional_email' => $faker->unique()->email(),
                        'professional_phone' => $faker->unique()->phoneNumber(),
                        'professional_photo' => $faker->imageUrl(360, 360, 'professional logo', true, 'professional logo'),
                        'professional_cover' => $faker->imageUrl(360, 360, 'professional logo', true, 'professional logo'),
                        'professional_title' => $faker->jobTitle(),
                        'paying' => true
                    ]);
                    if(!$newObj){
                        $person->delete();
                        return;
                    }
                    $objId = $newObj->professional_id;
                    $profileType = Profile::PROFESSIONAL;
                break;
            }
            if(!$newObj || !$profileType || !$person)
                continue;
            Profile::create([
                'person_id' => $person->person_id,
                'profile_type_id' => $objId,
                'profile_type' => $profileType
            ]);
        }
    }
}
