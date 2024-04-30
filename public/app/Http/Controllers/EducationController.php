<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    /**
     * Get all experiences by curriculum_id.
     * @param Int curriculum_id   - required
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(Education $education)
    {
        $education->delete();
        return response()->json($education);
    }
}
