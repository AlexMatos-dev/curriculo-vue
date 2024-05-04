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
     * @param Int curriculum_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {   
        Validator::validateParameters($this->request, [
            'per_page' => 'numeric',
            'curriculum_id' => 'numeric'
        ]);
        $visas = (new Visa())->getAllMyVisas(request('per_page', 100), $this->getProfessionalBySession()->professional_id, $this->getCurriculumBySession());
        return response()->json($visas);
    }

    /**
     * Creates a visa.
     * @param Int vicountry_id - required
     * @param Int visa_type - required
     * @param Int curriculum_id - required
     * @return \Illuminate\Http\JsonResponse 
     */
    public function store()
    {
        Validator::validateParameters($this->request, [
            'vicountry_id' => 'numeric|required',
            'visa_type' => 'numeric|required',
            'curriculum_id' => 'numeric|required'
        ]);
        Validator::checkExistanceOnTable([
            'vicountry_id' => ['data' => request('vicountry_id'), 'object' => ListCountry::class],
            'visa_type' => ['data' => request('visa_type'), 'object' => TypeVisas::class]
        ]);
        $visa = Visa::create($this->request->all());
        if(!$visa->save())
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
            'curriculum_id' => 'numeric|required'
        ]);
        Validator::checkExistanceOnTable([
            'vicountry_id' => ['data' => request('vicountry_id'), 'object' => ListCountry::class],
            'visa_type' => ['data' => request('visa_type'), 'object' => TypeVisas::class]
        ]);
        $visa = Visa::find(request('visa'));
        if(!$visa)
            Validator::throwResponse('visa not found', 400);
        $visa->update($this->request->all());
        if(!$visa)
            Validator::throwResponse('visa not updated', 500);
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
