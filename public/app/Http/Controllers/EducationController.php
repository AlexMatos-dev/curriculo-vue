<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\AreaOfStudy;
use App\Models\Curriculum;
use App\Models\DegreeType;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    /**
     * Get all experiences by curriculum_id.
     * @param Int edcurriculum_id
     * @param Int per_page
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        returnResponse(
            (new Education())->list($request->edcurriculum_id, request('per_page', 10))
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a new Education in storage.
     * @param String eddegree - required
     * @param Int edfield_of_study - required
     * @param String edinstitution - required
     * @param Int degree_type - required
     * @param Date edstart_date - required
     * @param Date edend_date
     * @param String eddescription
     */
    public function store(Request $request)
    {
        $request->validate([
            'edcurriculum_id'   => 'required',
            'eddegree'          => 'required',
            'edfield_of_study'  => 'required|numeric',
            'edinstitution'     => 'required',
            'edstart_date'      => 'required|date_format:Y-m-d|before:now',
            'degree_type'       => 'required|numeric',
            'edend_date'        => 'date_format:Y-m-d|before:now',
            'eddescription'     => 'max:400'
        ]);
        Validator::checkExistanceOnTable([
            'edfield_of_study' => ['object' => AreaOfStudy::class, 'data' => request('edfield_of_study')],
            'degree_type' => ['object' => DegreeType::class, 'data' => request('degree_type')]
        ]);
        if(request('edstart_date') && request('edend_date')){
            Validator::validateDates($request, [
                'edstart_date' => 'lower:edend_date',
                'edend_date' => 'bigger:edstart_date'
            ]);
        }
        $education = Education::create($request->all());

        returnResponse($education);
    }

    /**
     * Display the specified Education.
     * @param Int education_id - required
     */
    public function show(Education $education)
    {
        returnResponse($education);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Education $education)
    {
        //
    }

    /**
     * Update the specified Education in storage.
     * @param String eddegree - required
     * @param Int edfield_of_study - required
     * @param String edinstitution - required
     * @param Int degree_type - required
     * @param Date edstart_date - required
     * @param Date edend_date
     * @param String eddescription
     */
    public function update(Request $request, $educationId = null)
    {
        $education = Education::where('curriculums.cprofes_id', $this->getProfessionalBySession()->professional_id)->where('education_id', $educationId)
        ->leftJoin('curriculums', function($join){
            $join->on('curriculums.curriculum_id', '=', 'educations.edcurriculum_id');
        })->first();
        if(!$education)
            Validator::throwResponse(translate('education not found'), 400);
        $request->validate([
            'eddegree'          => 'required',
            'edfield_of_study'  => 'required|numeric',
            'edinstitution'     => 'required',
            'edstart_date'      => 'required|date_format:Y-m-d|before:now',
            'degree_type'       => 'required|numeric',
            'edend_date'        => 'date_format:Y-m-d|before:now',
            'eddescription'     => 'max:400'
        ]);
        Validator::checkExistanceOnTable([
            'edfield_of_study' => ['object' => AreaOfStudy::class, 'data' => request('edfield_of_study')],
            'degree_type' => ['object' => DegreeType::class, 'data' => request('degree_type')]
        ]);
        if(request('edstart_date') && request('edend_date')){
            Validator::validateDates($request, [
                'edstart_date' => 'lower:edend_date',
                'edend_date' => 'bigger:edstart_date'
            ]);
        }
        $education->update($request->all());
        returnResponse($education);
    }

    /**
     * Remove the specified Education from storage.
     * @param Int education_id - required
     */
    public function destroy($educationId)
    {
        $education = Education::where('curriculums.cprofes_id', $this->getProfessionalBySession()->professional_id)->where('education_id', $educationId)
        ->leftJoin('curriculums', function($join){
            $join->on('curriculums.curriculum_id', '=', 'educations.edcurriculum_id');
        })->first();
        if(!$education)
            Validator::throwResponse(translate('education not found'), 400);
        $education->delete();
        returnResponse($education);
    }
}
