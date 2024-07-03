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
                        $val = $val ? base64_encode($val) : null;
                    $data[$attr] = $val;
                }
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
}