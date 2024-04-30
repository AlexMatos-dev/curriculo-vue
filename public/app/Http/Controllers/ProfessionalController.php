<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\DataPerson;
use App\Models\Gender;
use App\Models\ListCity;
use App\Models\ListCountry;
use App\Models\ListState;
use App\Models\Person;
use App\Models\Professional;
use App\Models\Profile;
use Illuminate\Support\Str;
 

class ProfessionalController extends Controller
{
    /**
     * Updates logged person Professional account.
     * @param String professional_firstname - required
     * @param String professional_lastname - required
     * @param String professional_email - required
     * @param String professional_phone
     * @param String professional_title
     * @param String professional_photo
     * @param String professional_cover
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        Validator::validateParameters($this->request, [
            'professional_firstname' => 'required|max:250',
            'professional_lastname' => 'required|max:250',
            'professional_email' => 'required|max:250|email',
            'professional_phone' => 'max:20',
            'professional_title' => 'max:255',
        ]);
        $person = auth('api')->user();
        $dataToSet = [
            'professional_slug' => $person->makeSlug(request('professional_firstname'), request('professional_lastname')),
            'professional_firstname' => request('professional_firstname'),
            'professional_lastname' => request('professional_lastname'),
            'professional_email' => request('professional_email'),
            'professional_phone' => request('professional_phone'),
            'professional_title' => request('professional_title'),
            'person_id' => $person->person_id
        ];
        if(request('professional_photo')){
            $imageHandler = Validator::validateImage(request('professional_photo'));
            $dataToSet['professional_photo'] = base64_encode($imageHandler->generateImageThumbanil());
            $imageHandler->destroyFile();
        }
        if(request('professional_cover')){
            $imageHandler = Validator::validateImage(request('professional_cover'));
            $dataToSet['professional_cover'] = base64_encode($imageHandler->generateImageThumbanil());
            $imageHandler->destroyFile();
        }
        $professional = $person->getProfile(Profile::PROFESSIONAL);
        if(!$professional || ($professional && $professional->professional_email != request('professional_email')))
            Validator::validateParameters($this->request, ['professional_email' => 'unique:professionals']);
        $newProfessional = false;
        if(!$professional){
            $response = (new Professional())->saveProfessional($dataToSet);
            if(!$response)
                return response()->json(['message' => 'professional not found'], 500);
            $professional = $response;
            $newProfessional = true;
        }else{
            if(!$professional->saveProfessional($dataToSet))
                return response()->json(['message' => 'professional not updated'], 500);
        }
        if($newProfessional){
            $result = Profile::create([
                'person_id' => $person->person_id,
                'profile_type_id' => $professional->professional_id,
                'profile_type' => Profile::PROFESSIONAL
            ]);
            if(!$result){
                $professional->delete();
                return response()->json(['message' => 'professional not updated'], 500);
            }
        }
        return response()->json($professional);
    }

    /**
     * Updates logged person DataPerson account.
     * @param String dpdate_of_birth
     * @param String dpgender
     * @param String dpcity_id
     * @param String dpstate_id
     * @param String dppostal_code
     * @param String dpcountry_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDataPerson()
    {
        Validator::validateParameters($this->request, [
            'dpdate_of_birth' => 'date_format:Y-m-d',
            'dpgender' => 'integer',
            'dpcity_id' => 'integer',
            'dpstate_id' => 'integer',
            'dppostal_code' => 'max:20',
            'dpcountry_id' => 'integer'
        ]);
        if(request('dpcity_id') && !request('dpstate_id'))
            response()->json(['message' => "state is required"], 400)->send();
        if(request('dpstate_id') && !request('dpcountry_id'))
            response()->json(['message' => "country is required"], 400)->send();
        $objects = Validator::checkExistanceOnTable([
            'dpgender' => ['data' => request('dpgender'), 'object' => Gender::class],
            'dpcity_id' => ['data' => request('dpcity_id'), 'object' => ListCity::class],
            'dpstates_id' => ['data' => request('dpstate_id'), 'object' => ListState::class],
            'dpcountry_id' => ['data' => request('dpcountry_id'), 'object' => ListCountry::class]
        ]);
        if(array_key_exists('dpstates_id', $objects) && $objects['dpstates_id']->lstacountry_id != request('dpcountry_id'))
            response()->json(['message' => "state is not from country"], 400)->send();
        if(array_key_exists('dpcity_id', $objects) && $objects['dpcity_id']->lcitstates_id != request('dpstate_id'))
            response()->json(['message' => "city is not from state"], 400)->send();
        $person = auth('api')->user();
        $professional = $person->getProfile(Profile::PROFESSIONAL);
        if(!$professional)
            return response()->json(['message' => 'no professional found', 400]);
        $data = $this->request->all();
        $dataPerson = $professional->getDataPerson();
        $newDataPerson = false;
        if(!$dataPerson){
            $dataPerson = new DataPerson();
            $data['dpprofes_id'] = $professional->professional_id;
            $newDataPerson = true;
        }
        if(!$dataPerson->saveDataPerson($data))
            return response()->json(['message' => 'data person not updated', 400]);
        return response()->json($dataPerson);
    }
}
