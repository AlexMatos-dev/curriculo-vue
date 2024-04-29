<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
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
}