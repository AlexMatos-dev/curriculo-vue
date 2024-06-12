<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\Profile;

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
        $person = auth('api')->user();
        $recruiter = $person->getProfile(Profile::RECRUITER);
        if(!$recruiter)
            return response()->json(['message' => translate('no recruiter found')], 400);
        $imageHandler = Validator::validateImage(request('recruiter_photo'));
        $recruiterPhoto = base64_encode($imageHandler->generateImageThumbanil());
        $imageHandler->destroyFile();
        $recruiter->recruiter_photo = $recruiterPhoto;
        if(!$recruiter->save())
            return response()->json(['message' => translate('recruiter not updated')], 500);
        return response()->json($recruiter); 
    }
}
