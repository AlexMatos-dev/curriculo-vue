<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\DataPerson;
use App\Models\Gender;
use App\Models\JobModality;
use App\Models\ListCountry;
use App\Models\Professional;
use App\Models\ProfessionalProfession;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        $person = Auth::user();
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
                return response()->json(['message' => translate('professional not found')], 500);
            $professional = $response;
            $newProfessional = true;
        }else{
            if(!$professional->saveProfessional($dataToSet))
                return response()->json(['message' => translate('professional not updated')], 500);
        }
        if($newProfessional){
            $result = Profile::create([
                'person_id' => $person->person_id,
                'profile_type_id' => $professional->professional_id,
                'profile_type' => Profile::PROFESSIONAL
            ]);
            if(!$result){
                $professional->delete();
                return response()->json(['message' => translate('professional not updated')], 500);
            }
        }
        return response()->json($professional);
    }

    /**
     * Updates logged person DataPerson account.
     * @param String dpdate_of_birth
     * @param String dpgender
     * @param String dpcity
     * @param String dpstate
     * @param String dppostal_code
     * @param String dpcountry_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDataPerson()
    {
        Validator::validateParameters($this->request, [
            'dpdate_of_birth' => 'date_format:Y-m-d',
            'dpgender' => 'integer',
            'dpcity' => 'max:300',
            'dpstate' => 'max:300',
            'dppostal_code' => 'max:20',
            'dpcountry_id' => 'integer'
        ]);
        if(request('dpcity') && !request('dpstate'))
            response()->json(['message' => "state is required"], 400)->send();
        if(request('dpstate') && !request('dpcountry_id'))
            response()->json(['message' => "country is required"], 400)->send();
        $objects = Validator::checkExistanceOnTable([
            'dpgender' => ['data' => request('dpgender'), 'object' => Gender::class],
            'dpcountry_id' => ['data' => request('dpcountry_id'), 'object' => ListCountry::class]
        ]);
        $person = Auth::user();
        $professional = $person->getProfile(Profile::PROFESSIONAL);
        if(!$professional)
            return response()->json(['message' => translate('no professional found'), 400]);
        $data = $this->request->all();
        $dataPerson = $professional->getDataPerson();
        $newDataPerson = false;
        if(!$dataPerson){
            $dataPerson = new DataPerson();
            $data['dpprofes_id'] = $professional->professional_id;
            $newDataPerson = true;
        }
        if(!$dataPerson->saveDataPerson($data))
            return response()->json(['message' => translate('data person not updated'), 400]);
        return response()->json($dataPerson);
    }

    /**
     * Manages job modalities of professional, this method can ADD a new jobModality, REMOVE a current jobModality or LIST all jobModalities of professional
     * @param String action - possible values ['add', 'remove', 'list']
     * @param String job_modality_id - only required when 'action' = 'add'
     * @return \Illuminate\Http\JsonResponse
     */
    public function manageProfessionalJobModality()
    {
        $actions = ['add', 'remove', 'list'];
        if(!in_array(request('action'), $actions))
            return response()->json(['message' => translate('invalid action')], 400);
        $person = Auth::user();
        $professional = $person->getProfile(Profile::PROFESSIONAL);
        if(!$professional)
            return response()->json(['message' => translate('professional not found')], 400);
        $data = null;
        switch(request('action')){
            case 'add':
                Validator::validateParameters($this->request, [
                    'job_modality_id' => 'numeric|required'
                ]);
                Validator::checkExistanceOnTable([
                    'job_modality' => ['data' => request('job_modality_id'), 'object' => JobModality::class],
                ]);
                $result = $professional->syncJobModalities(request('job_modality_id'));
            break;
            case 'remove':
                $result = $professional->removeJobModality(request('job_modality_id'));
            break;
            case 'list':
                $result =  $professional->getJobModalities(false);
                $data = is_array($result) ? $result : [];
                $result = true;
            break;
        }
        if(!$result)
            return response()->json(['message' => translate('action not completed with success')], 500);
        return response()->json(['message' => translate('action performed'), 'data' => $data]);
    }

    /**
     * Manages professions of professional, this method can ADD, REMOVE or LIST all ProfessionProfessions of professional
     * @param String action - possible values ['add', 'remove', 'list']
     * @param String lprofession_id - only required when 'action' = 'add'
     * @param String started_working_at
     * @param String observations
     * @return \Illuminate\Http\JsonResponse
     */
    public function manageProfessionalProfessions()
    {
        $actions = ['add', 'remove', 'list'];
        if(!in_array(request('action'), $actions))
            return response()->json(['message' => 'invalid action'], 400);
        $person = Auth::user();
        $professional = $person->getProfile(Profile::PROFESSIONAL);
        if(!$professional)
            return response()->json(['message' => translate('professional not found')], 400);
        $data = null;
        switch(request('action')){
            case 'add':
                Validator::validateParameters($this->request, [
                    'lprofession_id' => 'numeric|required',
                    'started_working_at' => 'date_format:Y-m-d',
                    'observations' => 'max:500'
                ]);
                Validator::checkExistanceOnTable([
                    'profession' => ['data' => request('lprofession_id'), 'object' => ProfessionalProfession::class],
                ]);
                $started_working_at = request('started_working_at') ? Carbon::parse(request('started_working_at'))->format('Y-m-d H:i:s') : null;
                $result = $professional->syncProfessionalProfessions(request('lprofession_id'), $started_working_at, request('observations'));
            break;
            case 'remove':
                $result = $professional->removeProfessionProfession(request('lprofession_id'));
            break;
            case 'list':
                $result =  $professional->getProfessionProfessions(false);
                $data = is_array($result) ? $result : [];
                $result = true;
            break;
        }
        if(!$result)
            return response()->json(['message' => translate('action not completed with success')], 500);
        return response()->json(['message' => translate('action performed'), 'data' => $data]);
    }

    /**
     * Perform a search accordingly to sent parameters and returns a list of professionals paginated and with a 'match' percentage
     * @param Int page - default 1
     * @param Array skill_name
     * @param Array skproficiency_level
     * @param Array area_of_study
     * @param String certification_name
     * @param String exjob_title
     * @param Array dpgender
     * @param Array dpcity_id
     * @param Array dpstate_id
     * @param Array dpcountry_id
     * @param Array lalangue_id
     * @param Array laspeaking_level
     * @param Array lalistening_level
     * @param Array lawriting_level
     * @param Array lareading_level
     * @param Array visa_type
     * @param Array vicountry_id
     * @param Int professional_id
     * @return \Illuminate\Http\JsonResponse - Schema [
     *      "data": Array,
     *      "curent_page": int,
     *      "per_page": int,
     *      "last_page": int,
     * ]
     */
    public function index()
    {
        Validator::validateParameters($this->request, [
            'page' => 'integer',
            'skill_name' => 'array',
            'skproficiency_level' => 'array',
            'area_of_study' => 'array',
            'certification_name' => '',
            'exjob_title' => '',
            'dpgender' => 'array',
            'dpstate_id' => 'array',
            'dpcountry_id' => 'array',
            'lalangue_id' => 'array',
            'laspeaking_level' => 'array',
            'lalistening_level' => 'array',
            'lawriting_level' => 'array',
            'lareading_level' => 'array',
            'visa_type' => 'array',
            'vicountry_id' => 'array',
            'professional_id' => 'integer'
        ]);
        $page = request('page', 1);
        $perPage = request('per_page') > 100 ? 100 : request('per_page', 100);
        $professional = new Professional();
        $paying = $professional->listProfessionals($this->request, true, $perPage);
        $paying = $professional->splitJoinDataFromListedProfessions($paying, $this->request);
        $nonPaying = $professional->listProfessionals($this->request, false, $perPage);
        $nonPaying = $professional->splitJoinDataFromListedProfessions($nonPaying, $this->request);
        $total = count($paying) + count($nonPaying);
        $results = $professional->processListedProfessions([
            'paying'    => $paying,
            'nonPaying' => $nonPaying
        ], $perPage);
        $lastPage = ceil($total / $perPage);
        if($page < 1)
            $page = 1;
        if($page >= $lastPage)
            $page = $lastPage;
        if($page > 1){
            $nonPayingOffset = $results['status']['nonPaying'];
            $nonPaying = $professional->listProfessionals($this->request, false, $perPage, $nonPayingOffset);
            $nonPaying = $professional->splitJoinDataFromListedProfessions($nonPaying, $this->request);
            $payingOffset = $results['status']['paying'];
            $paying = $professional->listProfessionals($this->request, true, $perPage, $payingOffset);
            $paying = $professional->splitJoinDataFromListedProfessions($paying, $this->request);
            $results = $professional->processListedProfessions([
                'paying'    => $paying,
                'nonPaying' => $nonPaying
            ], $perPage);
        }
        return response()->json([
            'data' => $results['results'],
            'curent_page' => $page,
            'per_page' => $perPage,
            'last_page' => $lastPage
        ]);
    }

    /**
     * Gets professional data.
     * @param String professional_slug - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function find($professional_slug)
    {
        $professional = Professional::where('professional_slug', $professional_slug)->first();
        if(!$professional)
            Validator::throwResponse(translate('invalid professional slug'));
        return response()->json($professional->gatherInformation());
    }
}
