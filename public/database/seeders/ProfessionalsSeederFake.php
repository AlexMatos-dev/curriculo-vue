<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Country;
use App\Models\Curriculum;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Link;
use App\Models\ListCountry;
use App\Models\ListLangue;
use App\Models\Person;
use App\Models\Presentation;
use App\Models\Professional;
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

class ProfessionalsSeederFake extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areasOfStudy = json_decode(file_get_contents(storage_path('app/dbSourceFiles/areasOfStudy.json')), true);
        $faker = Faker::create('pt_BR');
        $defaultPassword = Hash::make('12345');
        $brazilCountry = ListCountry::where('lcountry_acronyn', 'br')->first();
        $ptLangue = ListLangue::where('llangue_acronyn', 'pt')->first();
        if(!$ptLangue || !$brazilCountry)
            return;
        $allTags = Tag::all()->toArray();
        $allProfeciencies = Proficiency::all()->toArray();
        $visaTypes = TypeVisas::all()->toArray();
        $gender = 'masculine';
        $edfield = [];
        for($i = 0; $i < 40; $i++){
            if($i >= 20)
                $gender = 'feminine';
            try {
                $person = Person::create([
                    "person_ddi" => substr($faker->phoneNumber(), 0, 2),
                    "person_phone" => $faker->phoneNumber(),
                    "person_langue" => $ptLangue->llangue_id,
                    "person_username" => $faker->firstName($gender) . ' ' . $faker->lastName($gender),
                    "person_email" => $faker->unique()->email(),
                    "person_password" => $defaultPassword
                ]);
                if(!$person)
                    continue;
                $professional = Professional::create([
                    'person_id' => $person->person_id,
                    'professional_slug' => '',
                    'professional_firstname' => $faker->firstName($gender),
                    'professional_lastname' => $faker->lastName($gender),
                    'professional_email' => $faker->unique()->email(),
                    'professional_phone' => $faker->unique()->phoneNumber(),
                    'professional_photo' => $faker->imageUrl(360, 360, 'professional logo', true, 'professional logo'),
                    'professional_cover' => $faker->imageUrl(360, 360, 'professional logo', true, 'professional logo'),
                    'professional_title' => $faker->jobTitle(),
                    'paying' => true
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
                    'clengua_id' => $ptLangue->llangue_id,
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
                        'eddegree' => $faker->jobTitle(),
                        'edfield_of_study' => $areasOfStudy[array_rand($areasOfStudy)]['en'],
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
                        'skproficiency_level' => $profeciency['proficiency_id'],
                        'experience_level' => $faker->randomFloat(1, 1, 100),
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
                $visaCountry = Country::where('country_name', $brazilCountry->lcountry_id)->first();
                if(!$visaCountry){
                    $visaCountry = Country::create([
                        'curriculum_id' => $curriculum->curriculum_id,
                        'country_name' => $brazilCountry->lcountry_id
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
            } catch (\Throwable $th) {
                continue;
            }
        }
    }
}
