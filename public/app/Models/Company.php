<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{   
    use SoftDeletes;

    protected $primaryKey = 'company_id';
    protected $table = 'companies';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_slug',
        'company_register_number',
        'company_name',
        'company_type',
        'company_logo',
        'company_cover_photo',
        'company_video',
        'company_email',
        'company_phone',
        'company_website',
        'company_description',
        'company_number_employees',
        'company_benefits',
        'paying'
    ];

    public function companyType()
    {
        return $this->belongsTo(CompanyType::class, 'company_type');
    }

    /**
     * Creates or Updates $this Company by sent data
     * @param Array - Schema [$attrName => $attrValue]
     * @return Profession|False
     */
    public function saveCompany($data = [])
    {
        $myAttributes = $this->fillable;
        foreach($data as $attr => $value){
            if(in_array($attr, $myAttributes))
                $this->{$attr} = $value;
        }
        if(!$this->save())
            return false;
        return $this;
    }

    /**
     * Add an admin to $this company
     * @param Int person_id
     * @param Bool withPrivilegies
     * @return Bool
     */
    public function addAdmin($person_id = null, $withPrivilegies = false)
    {
        if(!$person_id)
            return false;
        return CompanyAdmin::create([
            'company_id' => $this->company_id,
            'person_id' => $person_id,
            'has_privilegies' => $withPrivilegies
        ]);
    }

    /**
     * Checks if sent person_id is already an admin, if not Add it to $this company
     * @param Int person_id
     * @param Bool withPrivilegies
     * @return Bool
     */
    public function syncAdmins($person_id = null, $hasPrivilegies = false)
    {
        $companyAdmins = $this->getAdmins();
        foreach($companyAdmins as $admin){
            if($admin->person_id == $person_id)
                return true;
        }
        return $this->addAdmin($person_id, $hasPrivilegies);
    }

    /**
     * Verifies if person_id belongs to an admin of $this company
     * @param Int person_id
     * @return Bool
     */
    public function isAdmin($person_id = null)
    {
        $companyAdmins = $this->getAdmins();
        foreach($companyAdmins as $admin){
            if($person_id == $admin->person_id)
                return true;
        }
        return false;
    }

    /**
     * Removes an admin to $this company
     * @param Int person_id
     * @return Bool
     */
    public function removeAdmin($person_id = null)
    {
        if(!$this->isAdmin($person_id))
            return false;
        return CompanyAdmin::where('person_id', $person_id)->where('company_id', $this->company_id)->delete();
    }

    /**
     * Verifies if person_id belongs to an admin with privilegies of $this company
     * @param Int person_id
     * @return Bool
     */
    public function isAdminWithPrivilegies($person_id = null)
    {
        $companyAdmins = $this->getAdmins();
        foreach($companyAdmins as $admin){
            if($person_id == $admin->person_id && $admin->has_privilegies)
                return true;
        }
        return false;
    }

    /**
     * Grant or Revoke privilegies to sent person_id if it is an admin of $this company
     * @param Int person_id
     * @param Bool hasPrivilegies
     * @return Bool
     */
    public function hadleAdminPivilegies($person_id = null, $hasPrivilegies = false)
    {
        $companyAdmins = $this->getAdmins();
        foreach($companyAdmins as $admin){
            if($admin->person_id == $person_id){
                if($admin->has_privilegies == $hasPrivilegies)
                    return true;
                $admin->has_privilegies = $hasPrivilegies;
                return $admin->save();
            }
        }
        return false;
    }

    /**
     * Returns an array with all admins of $this company
     * @return ArrayOfCompanyAdmin
     */
    public function getAdmins()
    {
        return CompanyAdmin::where('company_id', $this->company_id)->get();
    }

    /**
     * Add a recruiter to $this company
     * @param Int person_id
     * @return Bool
     */
    public function addRecruiter($person_id = null)
    {
        if(!$person_id)
            return false;
        $recruiter = $this->isMyRecruiter($person_id, true);
        if($recruiter)
            return true;
        $recruiterResult = Recruiter::create([
            'company_id' => $this->company_id,
            'person_id' => $person_id
        ]);
        if(!$recruiterResult)
            return false;
        $profileResult = Profile::create([
            'person_id' => $person_id,
            'profile_type_id' => $recruiterResult->recruiter_id,
            'profile_type' => Profile::RECRUITER
        ]);
        if(!$profileResult){
            $recruiterResult->delete();
            return false;
        }
        return true;
    }

    /**
     * Removes an recruiter to $this company
     * @param Int person_id
     * @return Bool
     */
    public function removeRecruiter($person_id = null)
    {
        $recruiter = $this->isMyRecruiter($person_id, true);
        if(!$recruiter)
            return true;
        if(!Profile::where('profile_type_id', $recruiter->recruiter_id)->where('profile_type', Profile::RECRUITER)->delete())
            return false;
        $result = $recruiter->delete();
        return $result;
    }

    /**
     * Verifies if person_id (RECRUITER) belongs to $this company recruiters
     * @param Int person_id
     * @param Bool fetchObject
     * @return Bool|Recruiter - If fetchObject == true
     */
    public function isMyRecruiter($person_id = null, $fetchObject = false)
    {
        $recruiters = $this->getRecruiters();
        foreach($recruiters as $recruiter){
            if($recruiter->person_id == $person_id && $recruiter->company_id == $this->company_id)
                return $fetchObject ? $recruiter : true;
        }
        return false;
    }

    /**
     * Returns an array with all recruiter of $this company
     * @return ArrayOfRecruiters
     */
    public function getRecruiters()
    {
        return Recruiter::where('company_id', $this->company_id)->get();
    }

    /**
     * Checkes if company can receive notifications
     * @param Int chatMessageType - required
     * @return Bool - True = privacy allows | False = Privacy don't allow
     */
    public function checkPrivacy(String $chatMessageType = '')
    {
        if(!in_array($chatMessageType, [\App\Models\ChatMessage::CATEGORY_MESSAGE, \App\Models\ChatMessage::CATEGORY_NOTIFICATION]))
            return false;
        // Not implemented
        return true;
    }
}