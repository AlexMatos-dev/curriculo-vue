<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\Country;
use App\Models\ListCountry;
use App\Models\TypeVisas;
use App\Models\Visa;

class VisaController extends Controller
{
    /**
     * Get all visas of logged professional Curriculum.
     * @param Int per_page
     * @param Int vicurriculum_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {   
        Validator::validateParameters($this->request, [
            'per_page' => 'numeric',
            'vicurriculum_id' => 'numeric'
        ]);
        $visas = (new Visa())->getAllMyVisas(request('per_page', 100), $this->getProfessionalBySession()->professional_id, $this->getCurriculumBySession());
        return response()->json($visas);
    }

    /**
     * Creates a visa.
     * @param Int vicountry_id - required
     * @param Int visa_type - required
     * @param Int vicurriculum_id - required
     * @return \Illuminate\Http\JsonResponse 
     */
    public function store()
    {
        Validator::validateParameters($this->request, [
            'vicountry_id' => 'numeric|required',
            'visa_type' => 'numeric|required',
            'vicurriculum_id' => 'numeric|required'
        ]);
        Validator::checkExistanceOnTable([
            'country' => ['data' => request('vicountry_id'), 'object' => ListCountry::class],
            'visa_type' => ['data' => request('visa_type'), 'object' => TypeVisas::class]
        ]);
        $country = (new Country())->findOrCreateCountry([
            'curriculum_id' => request('vicurriculum_id'),
            'country_name' => request('vicountry_id')
        ]);
        if(!$country)
            Validator::throwResponse('country not found', 500);
        $values = $this->request->all();
        $values['vicountry_id'] = $country->country_id;
        $visa = Visa::create($values);
        if(!$visa)
            Validator::throwResponse('visa not created', 500);
        return response()->json($visa);
    }

    /**
     * Update the specified link in storage.
     * @param Int link_id - required
     * @param Int link_type - required
     * @param Int url - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        Validator::validateParameters($this->request, [
            'vicountry_id' => 'numeric|required',
            'visa_type' => 'numeric|required',
            'vicurriculum_id' => 'numeric|required'
        ]);
        $objects = Validator::checkExistanceOnTable([
            'listCountry' => ['data' => request('vicountry_id'), 'object' => ListCountry::class],
            'visa_type' => ['data' => request('visa_type'), 'object' => TypeVisas::class]
        ]);
        $visa = Visa::find(request('visa'));
        if(!$visa)
            Validator::throwResponse('visa not found', 400);
        $country = $visa->country();
        $isDifferent = false;
        if($country->country_name != $objects['listCountry']->lcountry_id){
            $isDifferent = ['countryId' => $country->country_id, 'curriculumId' => $visa->vicurriculum_id];
            $country = $country->findOrCreateCountry([
                'curriculum_id' => $visa->vicurriculum_id,
                'country_name' => request('vicountry_id')
            ]);
        }
        $values = $this->request->all();
        $values['vicountry_id'] = $country->country_id;
        $visa->update($values);
        if(!$visa)
            Validator::throwResponse('visa not updated', 500);
        if($isDifferent)
            $country->tryToRemove($isDifferent['countryId'], $isDifferent['curriculumId']);
        return response()->json($visa);
    }

    /**
     * Display the specified Visa.
     * @param Int visa - required (visa id)
     */
    public function show()
    {
        $visa = (new Visa())->isFromProfessionalCurriculum(request('visa'), $this->getProfessionalBySession()->professional_id);
        if(!$visa)
            Validator::throwResponse('visa not found', 400);
        return response()->json($visa);
    }

    /**
     * Remove the specified visa.
     * @param Int visa - required (visa id)
     * @return \Illuminate\Http\JsonResponse 
     */
    public function destroy()
    {
        $visa = (new Visa())->isFromProfessionalCurriculum(request('visa'), $this->getProfessionalBySession()->professional_id);
        if(!$visa)
            Validator::throwResponse('visa not found', 400);
        $countryId = $visa->vicountry_id;
        $curriculumId = $visa->vicurriculum_id;
        if(!$visa->delete())
            Validator::throwResponse('visa not removed', 500);
        (new Country())->tryToRemove($countryId, $curriculumId);
        return response()->json(['message' => 'visa removed']);
    }
}
