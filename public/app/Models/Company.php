<?php

namespace App\Models;

use App\Helpers\ModelUtils;
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
        'paying',
        'company_ddi'
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
     * Verifies if person_id belongs to an admin of $this company or a recruiter
     * @param Int person_id
     * @return Bool
     */
    public function isAdminOrRecruiter($person_id)
    {
        if($this->isAdmin($person_id) || $this->isMyRecruiter($person_id))
            return true;
        return false;
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

    /**
     * Get companies paginated
     * @param request request
     * @param Int page
     * @param Int perPage
     * @param Int coeficient
     * @return Array
     */
    public function getPaginatedCompanies($request, $page = 1, $perPage = 10, $coeficient = 5)
    {
        $nonPaying      = $this->listCompanies($request, false);
        $totalNonPaying = count($nonPaying);
        $paying         = $this->listCompanies($request, true);
        $totalPaying    = count($paying);
        $paginationData = $this->calculatePaginationData([
            'nonPaying' => $totalNonPaying,
            'paying'    => $totalPaying
        ], $page, $perPage, $coeficient);

        $data['nonPaying']    = [];
        $data['nonPayingIds'] = [];
        if($totalNonPaying > 0){
            $nonPayingIndex = $paginationData['offset']['nonPaying'];
            for($i = 0; $i < $paginationData['limit']['nonPaying']; $i++){
                if(!empty($nonPaying[$nonPayingIndex])){
                    $company = $nonPaying[$nonPayingIndex];
                    $data['nonPayingIds'][] = $company->company_id;
                    $data['nonPaying'][] = $company;
                }
                $nonPayingIndex++;
            }
            $nonPaying = $data['nonPaying'];
        }else{
            $nonPaying = [];
        }
        $data['paying']    = [];
        $data['payingIds'] = [];
        if($totalPaying > 0){
            $payingIndex = $paginationData['offset']['paying'];
            for($i = 0; $i < $paginationData['limit']['paying']; $i++){
                if(!empty($paying[$payingIndex])){
                    $company = $paying[$payingIndex];
                    $data['payingIds'][] = $company->company_id;
                    $data['paying'][] = $company;
                }
                $payingIndex++;
            }
            $paying = $data['paying'];
        }else{
            $paying = [];
        }
        $nonPaying = $data['nonPaying'];
        $paying    = $data['paying'];
        $companies = [];
        $tracker = 0;
        $indexes = [
            'paying'    => 0,
            'nonPaying' => 0
        ];
        if($totalPaying > 0){
            for($i = 0; $i < $perPage; $i++){
                if($tracker == $coeficient){
                    if(!empty($nonPaying[$indexes['nonPaying']]))
                        $companies[] = $nonPaying[$indexes['nonPaying']];
                    $tracker = 0;
                    $indexes['nonPaying']++;
                }else{
                    if(!empty($paying[$indexes['paying']]))
                        $companies[] = $paying[$indexes['paying']];
                    $indexes['paying']++;
                    $tracker++;
                }
            }
        }
        while(count($companies) < $perPage){
            if(!empty($nonPaying[$indexes['nonPaying']])){
                $companies[] = $nonPaying[$indexes['nonPaying']];
                $indexes['nonPaying']++;
            }else{
                break;
            }
        }
        $paginationData['results'] = $this->gatherCompanyInfo($companies, $request);
        return $paginationData;
    }

    /**
     * Lists jobs, filtering rsults accordingly to sent parameters
     * @param Illuminate\Http\Request $request - schema: [ 
     *     'company_register_number' => string, 'company_name' => string, 'company_type' => int, 'company_description' => string,
     *     'start_company_number_employees' => int, 'end_company_number_employees' => int, 'company_benefits' => string,
     * ]
     * @param Bool paying
     * @param Int limit
     * @param Int offset
     * @return Array
     */
    public function listCompanies(\Illuminate\Http\Request $request, $paying = false, $limit = 200, $offset = null, $byIds = [])
    {
        $limit = !is_numeric($limit) || $limit > 200 ? 200 : $limit;
        if($byIds)
            $limit = count($byIds);
        $query = Company::where('paying', $paying);
        if($byIds && !empty($byIds))
            $query->whereIn('company_id', $byIds);
        if ($request->has("company_register_number")) 
            $query->where("company_register_number", 'like', '%'.$request->company_register_number.'%');
        if ($request->has("company_name")) 
            $query->where("company_name", 'like', '%'.$request->company_name.'%');
        if ($request->has("company_type")) 
            $query->where("company_type", $request->company_type);
        if ($request->has("company_description")) 
            $query->where("company_description", 'like', '%'.$request->company_description.'%');
        if ($request->has("start_company_number_employees"))
            $query->where("start_company_number_employees", ">=", $request->start_company_number_employees);
        if ($request->has("end_company_number_employees"))
            $query->where("end_company_number_employees", "<=", $request->end_company_number_employees);
        if ($request->has("company_benefits")) 
            $query->where("company_benefits", 'like', '%'.$request->company_benefits.'%');
        if($limit)
            $query->limit($limit);
        if($offset)
            $query->offset($offset);
        $query->orderBy('created_at', 'desc');
        return $query->get();
    }

    /**
     * Calculates the offset for the query
     * @param Array totals
     * @param Int perPage
     * @param Int coeficient
     * @return Array - schema: ['offset' => Array, 'limit' => Array, 'perPage' => Int, 'lastPage' => Int]
     */
    public function calculatePaginationData($totals = [], $page = 1, $perPage = 10, $coeficient = 5)
    {
        $payingCount    = array_key_exists('paying', $totals)    ? $totals['paying']    : 0;
        $nonPayingCount = array_key_exists('nonPaying', $totals) ? $totals['nonPaying'] : 0;
        $totalCompanies = $payingCount + $nonPayingCount;
        if($payingCount == $nonPayingCount)
            $totalCompanies = (int)ceil($totalCompanies / 2);
        $maxPages     = (int)ceil($totalCompanies / $perPage);
        $logics = [
            'paying'        => 0,
            'nonPaying'     => 0,
            'maxPages'      => $maxPages,
            'offset'        => [
                'paying'    => 0,
                'nonPaying' => 0
            ],
            'limit'         => [
                'paying'    => 0,
                'nonPaying' => 0
            ]
        ];
        $index = 0;
        for($i = 0; $i < $perPage; $i++){
            if($index < $coeficient){
                $logics['paying']++;
            }else{
                $logics['nonPaying']++;
                $index = 0;
            }
            $index++;
            if($index >= $perPage)
                break;
        }
        if($payingCount == 0){
            $logics['paying']    = 0;
            $logics['nonPaying'] = $nonPayingCount < $perPage ? $nonPayingCount : $perPage;
        }
        $lastPage = $maxPages;
        if($page > $lastPage)
            $page = $lastPage;
        if($page < 1)
            $page = 1;
        $logics['offset']['paying']    = $page == 1 ? 0 : (int)$page * $logics['paying'];
        $logics['offset']['nonPaying'] = $page == 1 ? 0 : (int)$page * $logics['paying'];
        if($logics['offset']['paying'] > $payingCount && $logics['offset']['nonPaying'] < $nonPayingCount){
            $logics['limit']['paying']    = 0;
            $logics['limit']['nonPaying'] = $perPage;
        }else if($logics['offset']['paying'] < $payingCount && $logics['offset']['nonPaying'] > $nonPayingCount){
            $logics['limit']['nonPaying'] = 0;
            $logics['limit']['paying']    = $perPage;
        }else{
            $logics['limit']['paying']    = $logics['paying'];
            $logics['limit']['nonPaying'] = $logics['nonPaying'];
        }

        $total = $totals['nonPaying'] + $totals['paying'];
        if($page == 1 && $total != $perPage){
            $logics['limit']['paying']    = $totals['paying'];
            $logics['limit']['nonPaying'] = $totals['nonPaying'];
        }
        return [
            'limit'     => $logics['limit'],
            'offset'    => $logics['offset'],
            'lastPage'  => $lastPage,
            'page'      => $page
        ];
    }

    /**
     * Loops each result of Companies altering attributes and adding translations
     * @param Array companiesArray : schema['paying' => Array, 'nonPaying' => Array]
     * @param Request searchParameters
     * @return Array
     */
    public function gatherCompanyInfo($companiesArray = [], $searchParameters = null)
    {
        $info = [];
        $ids  = [];
        foreach($companiesArray as $company){
            if($company->company_type)
                $info['company_type'][] = $company->company_type;
            $ids[] = $company->company_id;
        }
        $listLangObj = new ListLangue();
        $languagesIso = $listLangObj->getNotOficialLangsIso();
        $companyTypes = ModelUtils::getTranslationsArray(
            new CompanyType(), 'name', !empty($info['company_type']) ? $info['company_type'] : [], 'company_type_id', $languagesIso
        );
        $languageIso = Session()->has('user_lang') ? Session()->get('user_lang') : ListLangue::DEFAULT_LANGUAGE;
        $companies = [];
        // have here a method to check the company privacy in order to determinate what information to send back
        $notAllowedAttr = ['company_email','company_phone','company_number_employees'];
        foreach($companiesArray as $company){
            $thisCompany = $company;
            $thisCompany->match = $this->generateCompatilityMatchOfCompany($thisCompany, $searchParameters);
            $companyType = '';
            if($thisCompany->company_id && array_key_exists($thisCompany->company_type, $companyTypes)){
                $companyType = $companyTypes[$thisCompany->company_type];
                $thisCompany->company_type = $companyType[$languageIso] ? $companyType[$languageIso] : ListLangue::DEFAULT_LANGUAGE;
            }else{
                $thisCompany->company_type = '';
            }
            if($thisCompany->company_logo)
                $thisCompany->company_logo = base64_encode($thisCompany->company_logo);
            if($thisCompany->company_cover_photo)
                $thisCompany->company_cover_photo = base64_encode($thisCompany->company_cover_photo);
            $thisCompany->company_created_at = ModelUtils::parseDateByLanguage($thisCompany->created_at, false, $languageIso);
            $thisCompany->company_updated_at = ModelUtils::parseDateByLanguage($thisCompany->updated_at, false, $languageIso);
            foreach($notAllowedAttr as $attr){
                unset($thisCompany->{$attr});
            }
            $companies[] = $thisCompany;
        }
        return $companies;
    }

    /**
     * Returns a number wiht the match of this Comapny by Request parametes
     * @param Company $company
     * @param \Illuminate\Http\Request $parameters
     * @return Float|Null
     */
    public function generateCompatilityMatchOfCompany($company, $parameters = null)
    {
        if(!$parameters || !is_object($parameters))
            return null;
        $validParameters = [
            'equal'   => ['company_type'],
            'like'    => ['company_register_number', 'company_name', 'company_description','company_benefits'],
            'in'      => ['company_number_employees::start_company_number_employees|end_company_number_employees'],
            'inArray' => [],
            'many'    => []
        ];
        $match = 0;
        $totalKeys = 0;
        // Gather all parameters
        foreach($validParameters as $checkType => $keys){
            foreach($keys as $key){
                if($checkType == 'many'){
                    $data = explode('::', $key);
                    $keyName = $data[0];
                    $keyValue = $parameters->has($keyName) ? $parameters->{$keyName} : null; 
                }else if($checkType == 'in'){
                    $data = explode('::', $key);
                    $keyValue = '';
                    if(!empty($data[1])){
                        foreach(explode('|', $data[1]) as $val){
                            $keyValue = $parameters->has($val) ? $parameters->{$val} : null;
                            if($keyValue){
                                $totalKeys++;
                            }
                        }
                    }
                    continue;
                }else{
                    $keyValue = $parameters->has($key) ? $parameters->{$key} : null;
                }
                if(!$keyValue)
                    continue;
                $totalKeys++;
            }
        }
        $matchCoeficient = $totalKeys > 0 ? $matchCoeficient = 100 / $totalKeys : 100;
        if($totalKeys == 0)
            return 100;
        // Read values
        foreach($validParameters as $checkType => $keys){
            foreach($keys as $key){
                if($checkType == 'many'){
                    $data = explode('::', $key);
                    $keyName = $data[0];
                    $keyId = count($data) == 2 ? $data[1] : null;
                    $keyValue = $parameters->has($keyName) ? $parameters->{$keyName} : null; 
                }else if($checkType == 'in'){
                    $data = explode('::', $key);
                    $keyValue = '';
                    if(!empty($data[1])){
                        foreach(explode('|', $data[1]) as $paramName){
                            $paramValue = $parameters->has($paramName) ? $parameters->{$paramName} : null;
                            if($paramValue)
                                $keyValue = true;
                        }
                        if($keyValue)
                            $keyValue = $data;
                    }
                }else{
                    $keyValue = $parameters->has($key) ? $parameters->{$key} : null;
                }
                if(!$keyValue)
                    continue;
                switch($checkType){
                    case 'equal':
                        if($keyValue == $company->{$key})
                            $match = $match + $matchCoeficient;
                    break;
                    case 'like':
                        if(is_numeric(strpos($company->{$key}, $keyValue)))
                            $match = $match + $matchCoeficient;
                    break;
                    case 'in':
                        $key = $keyValue[0];
                        $attr = explode('|', $keyValue[1]);
                        if(count($attr) != 2)
                            break;
                        $fromValue = (float)$parameters->{$attr[0]};
                        $toValue   = (float)$parameters->{$attr[1]};
                        $val       = (float)$company->{$key};
                        if(!$fromValue || !$toValue){
                            if(($fromValue && $val <= $fromValue) || ($toValue && $val >= $toValue))
                                $match = $match + $matchCoeficient;
                        }else if($fromValue <= $val && $toValue >= $val){
                            $match = $match + ($matchCoeficient * 2);
                        }
                    break;
                    case 'many':
                        if(!is_array($keyValue) || count($keyValue) < 1)
                            break;
                        $valid = [];
                        $attrName = str_replace('job_', '', $keyName);
                        $objectValue = $company->{$attrName . 'Ids'};
                        foreach($objectValue as $valObj){
                            if(in_array($valObj, $keyValue))
                                $valid[] = $valObj;
                        }
                        $totalSize = count($keyValue);
                        $thisVal = ($matchCoeficient / $totalSize) * count($valid);
                        $match = $match + $thisVal;
                    break;
                    case 'inArray':
                        $objectValue = $company->{$key};
                        $thisVal = $matchCoeficient / count($keyValue);
                        if(in_array($objectValue, $keyValue)){
                            $match = $match + $thisVal;
                        }
                    break;
                }
            }
        }
        $matchValue = number_format((float)$match, 2, '.', '');
        $matchValue = $matchValue > 100 ? 100 : $matchValue;
        return $matchValue < 0 ? 0 : $matchValue;
    }
}