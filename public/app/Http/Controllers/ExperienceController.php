<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\Curriculum;
use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    /**
     * Get all experiences by curriculum_id.
     * @param Int curriculum_id   - required
     * @param Int per_page
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $experience = Experience::where('excurriculum_id', $this->getCurriculumBySession()->curriculum_id);
        $experience =  $experience->paginate($request->pere_page);

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
            'exstart_date'      =>'required|date_format:Y-m-d',
            'exend_date'        =>'required|date_format:Y-m-d',
            'exdescription'     =>'max:500',
            'excurriculum_id'   =>'required|numeric'
        ]);
        Validator::validateDates($request, [
            'exstart_date' => 'lower:exend_date',
            'exend_date' => 'bigger:exstart_date'
        ]);
        $experience = Experience::create($request->all());

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
    public function update(Request $request, Experience $experience)
    {
        $request->validate([
            'exjob_title'       =>'required',
            'excompany_name'    =>'required',
            'exstart_date'      =>'required|date_format:Y-m-d',
            'exend_date'        =>'required|date_format:Y-m-d',
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
    public function destroy(Experience $experience)
    {
        $experience->delete();

        return response()->json($experience);
    }
}
