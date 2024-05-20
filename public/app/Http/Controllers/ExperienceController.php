<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    /**
     * Get all experiences by curriculum_id.
     * @param Int excurriculum_id
     * @param Int per_page
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $experience = Experience::where('excurriculum_id', $request->excurriculum_id);
        $experience =  $experience->paginate($request->per_page);

        return response()->json($experience);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a new Experience in storage.
     * @param String exjob_title - required
     * @param String excompany_name - required
     * @param Date exstart_date - required
     * @param Data exend_date - required
     * @param String exdescription
     */
    public function store(Request $request)
    {
        $request->validate([
            'exjob_title'       =>'required',
            'excompany_name'    =>'required',
            'exstart_date'      =>'required|date_format:Y-m-d|before:now',
            'exend_date'        =>'required|date_format:Y-m-d|before:now',
            'exdescription'     =>'max:500'
        ]);
        Validator::validateDates($request, [
            'exstart_date' => 'lower:exend_date',
            'exend_date' => 'bigger:exstart_date'
        ]);
        $data = $request->all();
        $data['excurriculum_id'] = $this->getCurriculumBySession()->curriculum_id;
        $experience = Experience::create($data);

        return response()->json($experience);

    }

    /**
     * Display the specified Experience.
     * @param Int experience_id - required
     */
    public function show(Experience $experience)
    {
        return response()->json($experience);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Experience $experience)
    {
        //
    }

    /**
     * Update Experience in storage.
     * @param Int experience_id - required
     * @param String exjob_title - required
     * @param String excompany_name - required
     * @param Date exstart_date - required
     * @param Data exend_date - required
     * @param String exdescription - required
     */
    public function update(Request $request, $experienceId = null)
    {
        $experience = Experience::where('curriculums.cprofes_id', $this->getProfessionalBySession()->professional_id)->where('experience_id', $experienceId)
        ->leftJoin('curriculums', function($join){
            $join->on('curriculums.curriculum_id', '=', 'experiences.excurriculum_id');
        })->first();
        if(!$experience)
            Validator::throwResponse('experience not found', 400);
        $request->validate([
            'exjob_title'       =>'required',
            'excompany_name'    =>'required',
            'exstart_date'      =>'required|date_format:Y-m-d|before:now',
            'exend_date'        =>'required|date_format:Y-m-d|before:now',
            'exdescription'     =>'max:500'
        ]);
        Validator::validateDates($request, [
            'exstart_date' => 'lower:exend_date',
            'exend_date' => 'bigger:exstart_date'
        ]);

        $experience->update($request->all());

        return $experience;
    }

    /**
     * Remove the specified experience from storage.
     * @param Int experience_id - required
     */
    public function destroy($experienceId)
    {
        $experience = Experience::where('curriculums.cprofes_id', $this->getProfessionalBySession()->professional_id)->where('experience_id', $experienceId)
        ->leftJoin('curriculums', function($join){
            $join->on('curriculums.curriculum_id', '=', 'experiences.excurriculum_id');
        })->first();
        if(!$experience)
            Validator::throwResponse('experience not found', 400);
        $experience->delete();

        return response()->json($experience);
    }
}
