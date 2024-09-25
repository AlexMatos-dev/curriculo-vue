<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\CompanyRecruiter;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class RecruiterController extends Controller
{
    /**
     * Updates logged person Recruiter account.
     * @param String recruiter_photo - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        Validator::validateParameters($this->request, [
            'recruiter_photo' => 'required'
        ]);
        $person = Auth::user();
        $recruiter = $person->getProfile(Profile::RECRUITER);
        if(!$recruiter)
            returnResponse(['message' => translate('no recruiter found')], 400);
        $imageHandler = Validator::validateImage(request('recruiter_photo'));
        $recruiterPhoto = base64_encode($imageHandler->generateImageThumbanil());
        $imageHandler->destroyFile();
        $recruiter->recruiter_photo = $recruiterPhoto;
        if(!$recruiter->save())
            returnResponse(['message' => translate('recruiter not updated')], 500);
        returnResponse($recruiter); 
    }

    /**
     * Accepts a company invitation to be a recruiter
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptInvitation()
    {
        $recruiter = $this->getRecruiterBySession();
        $companyRecruiter = CompanyRecruiter::where('recruiter_id', $recruiter->recruiter_id)->first();
        if(!$companyRecruiter)
            returnResponse(['message' => translate('invitation not found')], 404);
        if($companyRecruiter->status == CompanyRecruiter::ACTIVE_RECRUITER)
            returnResponse(['message' => translate('invitation already accepted')], 500);
        $companyRecruiter->status = CompanyRecruiter::ACTIVE_RECRUITER;
        if(!$companyRecruiter->save())
            returnResponse(['message' => translate('invitation not accepted, try again')], 500);
        returnResponse([
            'message' => translate('invitation accepted'), 
            'data' => $companyRecruiter->listByCompany($companyRecruiter->company_id, null, $companyRecruiter->company_recruiter_id)
        ], 200);
    }

    /**
     * Refuses a company invitation to be a recruiter
     * @param String status - either ('active', 'invited' or 'refused')
     * @return \Illuminate\Http\JsonResponse
     */
    public function refuseInvitation()
    {
        $recruiter = $this->getRecruiterBySession();
        $companyRecruiter = CompanyRecruiter::where('recruiter_id', $recruiter->recruiter_id)->first();
        if(!$companyRecruiter)
            returnResponse(['message' => translate('invitation not found')], 404);
        if($companyRecruiter->status == CompanyRecruiter::REFUSED_INVITATION)
            returnResponse(['message' => translate('invitation already declined')], 500);
        $companyRecruiter->status = CompanyRecruiter::REFUSED_INVITATION;
        if(!$companyRecruiter->save())
            returnResponse(['message' => translate('invitation not declined, try again')], 500);
        returnResponse([
            'message' => translate('invitation declined'), 
            'data' => $companyRecruiter->listByCompany($companyRecruiter->company_id, null, $companyRecruiter->company_recruiter_id)
        ], 200);
    }

    /**
     * Gets a list of this recruiter invitations
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvitations()
    {
        $recruiter = $this->getRecruiterBySession();
        returnResponse([
            'message' => translate('invitations gathered'), 
            'data' => (new CompanyRecruiter())->listByCompany(null, request('status'), $recruiter->recruiter_id)
        ], 200);
    }
}
