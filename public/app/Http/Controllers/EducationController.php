<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    /**
     * Get all experiences by curriculum_id.
     * @param Int curriculum_id - required
     * @param Int per_page
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $curriculum = Curriculum::where('curriculum_id', $request->curriculum_id)->first();

        $education = Education::where('edcurriculum_id',$curriculum->curriculum_id );
        $education = $education->paginate($request->per_page);

        return response()->json($education);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newl Education in storage.
     * @param Int edcurriculum_id - required
     * @param String eddegree - required
     * @param String edfield_of_study - required
     * @param String edinstitution - required
     * @param Date edstart_date - required
     * @param Date edend_date - required
     * @param String eddescription - required
     */
    public function store(Request $request)
    {
        $request->validate([
            'edcurriculum_id'   => 'required',
            'eddegree'          => 'required',
            'edfield_of_study'  => 'required',
            'edinstitution'     => 'required',
            'edstart_date'      => 'required',
            'edend_date'        => 'required',
            'eddescription'     => 'required'
        ]);

        $education = Education::create($request->all());

        return response()->json($education);
    }

    /**
     * Display the specified Education.
     * @param Int education_id - required
     */
    public function show(Education $education)
    {
        return response()->json($education);
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
     * @param Int education_id - required
     * @param Int edcurriculum_id - required
     * @param String eddegree - required
     * @param String edfield_of_study - required
     * @param String edinstitution - required
     * @param Date edstart_date - required
     * @param Date edend_date - required
     * @param String eddescription - required
     */
    public function update(Request $request, Education $education)
    {
        $request->validate([
            'edcurriculum_id'   => 'required',
            'eddegree'          => 'required',
            'edfield_of_study'  => 'required',
            'edinstitution'     => 'required',
            'edstart_date'      => 'required',
            'edend_date'        => 'required',
            'eddescription'     => 'required'
        ]);

        $education->update($request->all());

        return response()->json($education);
    }

    /**
     * Remove the specified Education from storage.
     * @param Int education_id - required
     */
    public function destroy(Education $education)
    {
        $education->delete();
        return response()->json($education);
    }
}
