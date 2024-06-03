<?php

namespace Database\Seeders;

use App\Models\AreaOfStudy;
use App\Models\Certification;
use App\Models\Country;
use App\Models\Curriculum;
use App\Models\DataPerson;
use App\Models\DegreeType;
use App\Models\Education;
use App\Models\Experience;
use App\Models\JobModality;
use App\Models\Language;
use App\Models\Link;
use App\Models\ListCity;
use App\Models\ListCountry;
use App\Models\ListLangue;
use App\Models\Person;
use App\Models\Presentation;
use App\Models\Professional;
use App\Models\ProfessionalJobModality;
use App\Models\Proficiency;
use App\Models\Profile;
use App\Models\Reference;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\TypeVisas;
use App\Models\Visa;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class CreateFakeProfessionals extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $limit = false;
        while(!is_numeric($limit)){
            $limit = $this->command->ask('Enter the number of Professionals records to create');
        }
        if($limit < 1)
            $limit = 1;
        $faker = Faker::create('pt_BR');
        $languagesArray = ListLangue::get()->toArray();
        if(empty($languagesArray))
            return;
        $brCountry = ListCountry::where('lcountry_acronyn', 'br')->first();
        if(!$brCountry)
            return;
        $brCity = ListCity::where('listcountries.lcountry_acronyn', 'br')->leftJoin('liststates', function($join){
            $join->on('listcities.lcitstates_id', '=', 'liststates.lstates_id');
        })->leftJoin('listcountries', function($join){
            $join->on('liststates.lstacountry_id', '=', 'listcountries.lcountry_id');
        })->get();
        if(empty($brCity))
            return;
        $brCity = $brCity->toArray();
        $defaultPassword = Hash::make('12345');
        $allTags = Tag::all()->toArray();
        $allProfeciencies = Proficiency::where('category', Proficiency::CATEGORY_LEVEL)->get()->toArray();
        $allLangProfeciencies = Proficiency::where('category', Proficiency::CATEGORY_LANGUAGE)->get()->toArray();
        $areasOfStudy = AreaOfStudy::get()->toArray();
        $visaTypes = TypeVisas::all()->toArray();
        $jobModalitiesArray = JobModality::all()->toArray();
        $degreeArray = DegreeType::all()->toArray();
        $allLanguages = ListLangue::all()->toArray();
        // STARTING
        $gender = 'masculine';
        $half = (int) $limit / 2;
        for($i = 0; $i < $limit; $i++){
            if($i >= $half)
                $gender = 'feminine';
            $email = $faker->email();
            if(Person::where('person_email', $email)->first())
                continue;
            $langObj = $languagesArray[array_rand($languagesArray)];
            $person = Person::create([
                'person_username' => $faker->firstName($gender) . ' ' . $faker->lastName($gender),
                'person_email' => $email,
                'person_password' => $defaultPassword,
                'person_ddi' => substr($faker->phoneNumber(), 0, 2),
                'person_phone' => $faker->phoneNumber(),
                'person_langue' => $langObj['llangue_id'],
                'last_login' => $faker->dateTime()
            ]);
            if(!$person)
                continue;
            $firstName = $faker->firstName($gender);
            $lastName = $faker->lastName($gender);
            $professional = Professional::create([
                'person_id' => $person->person_id,
                'professional_slug' => $person->makeSlug($firstName, $lastName),
                'professional_firstname' => $firstName,
                'professional_lastname' => $lastName,
                'professional_email' => $faker->unique()->email(),
                'professional_phone' => $faker->unique()->phoneNumber(),
                'professional_photo' => $faker->imageUrl(360, 360, 'professional logo', true, 'professional logo'),
                'professional_cover' => $faker->imageUrl(360, 360, 'professional logo', true, 'professional logo'),
                'professional_title' => $faker->jobTitle(),
                'currently_working' => $faker->boolean(),
                'avaliable_to_travel' => $faker->boolean(),
                'paying' => $faker->boolean()
            ]);
            if(!$professional)
                    $professional->delete();
            $profile = Profile::create([
                'person_id' => $person->person_id,
                'profile_type_id' => $person->person_id,
                'profile_type' => Profile::PROFESSIONAL
            ]);
            if(!$profile){
                $professional->delete();
                $person->delete();
            }
            $curriculum = Curriculum::create([
                'cprofes_id' => $professional->professional_id,
                'clengua_id' => $langObj['llangue_id'],
                'curriculum_type' => Curriculum::TYPE_INFO
            ]);
            if(!$curriculum){
                $profile->delete();
                $professional->delete();
                $person->delete();
            }
            // Add education
            for($u = 0; $u < 2; $u++){
                $education = Education::create([
                    'edcurriculum_id' => $curriculum->curriculum_id,
                    'eddegree' => $faker->company(),
                    'degree_type' => $degreeArray[array_rand($degreeArray)]['degree_type_id'],
                    'edfield_of_study' => $areasOfStudy[array_rand($areasOfStudy)]['area_of_study_id'],
                    'edinstitution' => $faker->company(),
                    'edstart_date' => '2020-01-01',
                    'edend_date' => '2025-12-20',
                    'eddescription' => $faker->realText(200)
                ]);
            }
            // Add experience
            for($u = 0; $u < 5; $u++){
                $experience = Experience::create([
                    'excurriculum_id' => $curriculum->curriculum_id,
                    'exjob_title' => $faker->jobTitle(),
                    'excompany_name' => $faker->company(),
                    'exstart_date' => $faker->date('Y-m-d'),
                    'exend_date' => $faker->date('Y-m-d'),
                    'exdescription' => $faker->realText(200)
                ]);
            }
            // Add certifications
            for($u = 0; $u < 5; $u++){
                $certification = Certification::create([
                    'cercurriculum_id' => $curriculum->curriculum_id,
                    'certification_name' => $faker->jobTitle(),
                    'cerissuing_organization' => $faker->company(),
                    'cerissue_date' => $faker->date('Y-m-d'),
                    'cert_hours' => $faker->numberBetween(10, 500),
                    'cerdescription' => $faker->realText(200),
                    'cerlink' => substr($faker->url(), 0, 100)
                ]);
            }
            // Add skills
            for($u = 0; $u < 20; $u++){
                $tag = $allTags[array_rand($allTags)];
                $profeciency = $allProfeciencies[array_rand($allProfeciencies)];
                $skill = Skill::create([
                    'skcurriculum_id' => $curriculum->curriculum_id,
                    'skill_name' => $tag['tags_id'],
                    'skproficiency_level' => $profeciency['proficiency_id']
                ]);
            }
            // Add references
            for($u = 0; $u < 5; $u++){
                $reference = Reference::create([
                    'refcurriculum_id' => $curriculum->curriculum_id,
                    'reference_name' => $faker->jobTitle(),
                    'reference_email' => $faker->unique()->email,
                    'refrelationship' => $faker->realText(10)
                ]);
            }
            // Add presentation
            for($u = 0; $u < 5; $u++){
                $presentation = Presentation::create([
                    'precurriculum_id' => $curriculum->curriculum_id,
                    'presentation_text' => $faker->realText(300)
                ]);
            }
            // Add visa
            $visaCountry = Country::where('country_name', $brCountry->lcountry_id)->first();
            if(!$visaCountry){
                $visaCountry = Country::create([
                    'curriculum_id' => $curriculum->curriculum_id,
                    'country_name' => $brCountry->lcountry_id
                ]);
            }
            for($u = 0; $u < 5; $u++){
                if(!$visaCountry)
                    continue;
                $visaType = $visaTypes[array_rand($visaTypes)];
                $visa = Visa::create([
                    'vicurriculum_id' => $curriculum->curriculum_id,
                    'vicountry_id' => $visaCountry->country_id,
                    'visa_type' => $visaType['typevisas_id']
                ]);
            }
            // Add link
            for($u = 0; $u < 20; $u++){
                $link = Link::create([
                    'curriculum_id' => $curriculum->curriculum_id,
                    'link_type' => Link::LINK_TYPES[array_rand(Link::LINK_TYPES)],
                    'url' => substr($faker->url(), 0, 100)
                ]);
            }
            // Job Modality
            for($u = 0; $u < 20; $u++){
                $professionalJobModality = ProfessionalJobModality::create([
                    'professional_id' => $professional->professional_id,
                    'job_modality_id' => $jobModalitiesArray[array_rand($jobModalitiesArray)]['job_modality_id']
                ]);
            }
            // Languages
            for($u = 0; $u < 3; $u++){
                $thisLanguage = $allLanguages[array_rand($allLanguages)]['llangue_id'];
                $speaking = $allLangProfeciencies[array_rand($allLangProfeciencies)]['proficiency_id'];
                $listening = $allLangProfeciencies[array_rand($allLangProfeciencies)]['proficiency_id'];
                $writting = $allLangProfeciencies[array_rand($allLangProfeciencies)]['proficiency_id'];
                $reading = $allLangProfeciencies[array_rand($allLangProfeciencies)]['proficiency_id'];
                $language = Language::create([
                    'lacurriculum_id' => $curriculum->curriculum_id,
                    'lalangue_id' => $thisLanguage,
                    'laspeaking_level' => $speaking,
                    'lalistening_level' => $listening,
                    'lawriting_level' => $writting,
                    'lareading_level' => $reading
                ]);
            }
            $city = $brCity[array_rand($brCity)];
            $dataPerson = DataPerson::create([
                'dpprofes_id' => $professional->professional_id,
                'dpdate_of_birth' => $faker->date('Y-m-d'),
                'dpgender' => $i > $half ? 2 : 1,
                'dpcity_id' => $city['lcity_id'],
                'dpstate_id' => $city['lcitstates_id'],
                'dppostal_code' => $faker->postcode(),
                'dpcountry_id' => $brCountry->lcountry_id
            ]);
        }
    }
}
