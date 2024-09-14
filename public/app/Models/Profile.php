<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes;
    
    const PROFESSIONAL = 'professionals';
    const RECRUITER    = 'recruiters';
    const COMPANY      = 'companies';

    const PROFILE_TYPE_REFERENCES = [
        'professionals' => Professional::class,
        'recruiters'    => Recruiter::class,
        'companies'     => Company::class
    ];

    protected $primaryKey = 'profile_id';
    protected $table = 'profiles';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'person_id',
        'profile_type_id',
        'profile_type'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id')->first();
    }

    /**
     * Get logged $person profile type objects
     * @param Int personId
     * @return Array schema [
     *      'professionals' => Professional object | Null,
     *      'recruiters'    => Recruiter object | Null,
     *      'companies'     => Company object | Null,
     *      'person'        => Person object
     * ]
     */
    public function getProfilesByPersonId($personId = null)
    {
        $myProfiles = [
            'professionals' => null,
            'companies' => null,
            'recruiters' => null,
        ];
        $profiles = Profile::where('person_id', $personId)->get();
        foreach($profiles as $profile){
            $profileType = $profile->profile_type;
            if(!array_key_exists($profileType, $myProfiles) || !array_key_exists($profileType, $this::PROFILE_TYPE_REFERENCES))
                continue;
            $objectPath = $this::PROFILE_TYPE_REFERENCES[$profileType];
            $object = (new $objectPath())::find($profile->profile_type_id);
            if($object){
                $data = [];
                foreach($object->getFillable() as $attr){
                    $val = $object->{$attr};
                    if(in_array($attr, ['company_cover_photo','company_logo','professional_photo','recruiter_photo']))
                        $val = $val ? $val : null;
                    $data[$attr] = $val;
                }
                $data[$object->getKeyName()] = $object->{$object->getKeyName()};
                $myProfiles[$profileType] = $data;
            }
        }
        return $myProfiles;
    }

    /**
     * Get logged $person profile type
     * @param Int personId
     * @return Array schema [
     *      'professionals' => Profile object | Null,
     *      'recruiters'    => Profile object | Null,
     *      'companies'     => Profile object | Null
     * ]
     */
    public function getProfileStatus($personId = null)
    {
        $myProfiles = Profile::select('profile_type_id', 'profile_type')->where('person_id', $personId)->get();
        $data = [
            'professionals' => null,
            'recruiters' => null,
            'companies' => null
        ];
        foreach($myProfiles as $profile){
            $data[$profile->profile_type] = $profile;
        }
        return $data;
    }

    public function createProfile(Person $person, String $profileType)
    {
        $objectId = null;
        $object  = null;
        try {
            switch($profileType){
                case Profile::PROFESSIONAL:
                    $professionalName = explode(' ', $person->person_username);
                    if(count($professionalName) == 1){
                        $professionalName = [$professionalName[0], ucfirst($professionalName[0][0])];
                    }
                    $professional = (new Professional())->saveProfessional([
                        'professional_firstname' => $professionalName[0], 
                        'professional_lastname' => $professionalName[1], 
                        'professional_email' => $person->person_email,
                        'professional_slug' => $person->makeSlug($professionalName[0], $professionalName[0][0]),
                        'person_id' => $person->person_id
                    ]);
                    if(!$professional)
                        return false;
                    $objectId = $professional->professional_id;
                    $object   = $professional;
                break;
                case Profile::COMPANY:
                    $company = (new Company())->saveCompany([
                        'company_slug'  => \Illuminate\Support\Str::slug($person->person_username),
                        'company_name'  => $person->person_username,
                        'company_email' => $person->person_email
                    ]);
                    if(!$company)
                        return false;
                    $company->syncAdmins($person->person_id, true);
                    $objectId = $company->company_id;
                    $object   = $company;
                break;
                case Profile::RECRUITER:
                    $recruiter = Recruiter::create([
                        'person_id' => $person->person_id
                    ]);
                    if(!$recruiter)
                        return false;
                    $objectId = $recruiter->recruiter_id;
                    $object   = $recruiter;
                break;
            }
        } catch (\Throwable $th) {
            return false;
        }
        $result = Profile::create([
            'person_id'       => $person->person_id,
            'profile_type_id' => $objectId,
            'profile_type'    => $profileType
        ]);
        if(!$result && $object)
            $object->delete();
        return true;
    }
}