<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\Company;
use App\Models\CompanyType;
use App\Models\Person;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Updates logged person Company account.
     * @param String company_register_number - required
     * @param String company_name - required
     * @param Int    company_type - required
     * @param String company_video
     * @param String company_email
     * @param String company_phone
     * @param String company_website
     * @param String company_description
     * @param String company_number_employees
     * @param String company_benefits
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        Validator::validateParameters($this->request, [
            'company_register_number' => 'required|max:100',
            'company_name' => 'required|max:300',
            'company_type' => 'required|integer',
            'company_video' => 'max:150',
            'company_email' => 'required|email|max:150',
            'company_phone' => 'max:20',
            'company_website' => 'max:100',
            'company_description' => 'max:500',
            'company_number_employees' => 'numeric|not_in:0',
            'company_benefits' => 'max:4294967295'
        ]);
        Validator::checkExistanceOnTable([
            'company_type' => ['object' => CompanyType::class, 'data' => request('company_type')]
        ]);
        $person = Auth::user();
        $dataToSet = [
            'company_register_number' => request('company_register_number'),
            'company_slug' => $person->makeSlug(request('company_name'), null),
            'company_name' => request('company_name'),
            'company_type' => request('company_type'),
            'company_video' => request('company_video'),
            'company_email' => request('company_email'),
            'company_phone' => request('company_phone'),
            'company_website' => request('company_website'),
            'company_description' => request('company_description'),
            'company_number_employees' => request('company_number_employees'),
            'company_benefits' => request('company_benefits'),
            'person_id' => $person->person_id
        ];
        if(request('company_logo')){
            $imageHandler = Validator::validateImage(request('company_logo'));
            $dataToSet['company_logo'] = base64_encode($imageHandler->generateImageThumbanil());
            $imageHandler->destroyFile();
        }
        if(request('company_cover_photo')){
            $imageHandler = Validator::validateImage(request('company_cover_photo'));
            $dataToSet['company_cover_photo'] = base64_encode($imageHandler->generateImageThumbanil());
            $imageHandler->destroyFile();
        }
        $company = $person->getProfile(Profile::COMPANY);
        if(!$company || ($company && $company->company_email != request('company_email')))
            Validator::validateParameters($this->request, ['company_email' => 'unique:companies']);
        $newCompany = false;
        if(!$company){
            $response = (new Company())->saveCompany($dataToSet);
            if(!$response)
                return response()->json(['message' => translate('professional not found')], 500);
            $company = $response;
            $newCompany = true;
        }else{
            if(!$company->isAdmin($person->person_id))
                return response()->json(['message' => translate('not an admin of company')], 401);
            if(!$company->saveCompany($dataToSet))
                return response()->json(['message' => translate('company not updated')], 500);
        }
        if($newCompany){
            $result = Profile::create([
                'person_id' => $person->person_id,
                'profile_type_id' => $company->company_id,
                'profile_type' => Profile::COMPANY
            ]);
            if(!$result){
                $company->delete();
                return response()->json(['message' => translate('company not updated')], 500);
            }
            $company->syncAdmins($person->person_id, true);
        }
        return response()->json($company);
    }

    /**
     * Manages company admins, this method can ADD a new admin, REMOVE a current admin, GRANT privilegies and REVOKE privilegies of an admin
     * Obs: Only admins with privilegies can perform an action!
     * @param String action - possible values ['add', 'remove', 'grant', 'revoke']
     * @param Int person_id - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function manageCompanyAdmin()
    {
        $actions = ['add', 'remove', 'grant', 'revoke', 'list'];
        if(!in_array(request('action'), $actions))
            return response()->json(['message' => translate('invalid action')], 400);
        $person = Auth::user();
        $company = Session()->get('company') ? $this->getCompanyBySession() : $person->getProfile(Profile::COMPANY);
        $targetPerson = Person::find(request('person_id'));
        if(request('action') != 'list' && (!$targetPerson || $person->person_id == $targetPerson->person_id))
            return response()->json(['message' => translate('person is invalid')], 400);
        $data = [];
        switch(request('action')){
            case 'add':
                $result = $company->syncAdmins(request('person_id'));
            break;
            case 'remove':
                $result = $company->removeAdmin(request('person_id'));
            break;
            case 'grant':
                $result = $company->hadleAdminPivilegies(request('person_id'), true);
            break;
            case 'revoke':
                $result = $company->hadleAdminPivilegies(request('person_id'), false);
            break;
            case 'list':
                $data = $company->getAdmins();
                $result = true;
            break;
        }
        if(!$result)
            return response()->json(['message' => translate('action not completed with success')], 500);
        return response()->json(['message' => translate('action performed'), 'data' => $data]);
    }

    /**
     * Manages company recruiters, this method can ADD a new recruiter, REMOVE a current recruit or LIST all recruiters of company
     * Obs: Only admins with privilegies can perform an action!
     * @param String action - possible values ['add', 'remove or 'list]
     * @param Int person_id - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function manageCompanyRecruiter()
    {
        $actions = ['add', 'remove', 'list'];
        if(!in_array(request('action'), $actions))
            return response()->json(['message' => translate('invalid action')], 400);
        $person = Auth::user();
        $company = Session()->get('company') ? $this->getCompanyBySession() : $person->getProfile(Profile::COMPANY);
        $targetPerson = Person::find(request('person_id'));
        if(request('action') != 'list' && (!$targetPerson || $person->person_id == $targetPerson->person_id))
            return response()->json(['message' => translate('person is invalid')], 400);
        switch(request('action')){
            case 'add':
                $result = $company->addRecruiter($targetPerson->person_id);
            break;
            case 'remove':
                $result = $company->removeRecruiter($targetPerson->person_id);
            break;
            case 'list':
                $result = true;
            break;
        }
        if(!$result)
            return response()->json(['message' => translate('action not completed with success')], 500);
        return response()->json(['message' => translate('action performed'), 'data' => $company->getRecruiters()]);
    }
}
