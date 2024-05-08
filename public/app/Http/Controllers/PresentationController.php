<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Presentation;
use Illuminate\Http\Request;

class PresentationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $presentation = Presentation::where('precurriculum_id', $this->getCurriculumBySession()->curriculum_id);
        $presentation = $presentation->paginate($request->per_page);
        return response()->json($presentation);
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
            'precurriculum_id'   =>  'required',
            'presentation_text'  =>  'required'

        ]);

        $presentation = Presentation::create($request->all());

        return response()->json($presentation);
    }

    /**
     * Display the specified resource.
     */
    public function show(Presentation $presentation)
    {
        return response()->json($presentation);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presentation $presentation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Presentation $presentation)
    {
        $request->validate([
            'precurriculum_id'   =>  'required',
            'presentation_text'  =>  'required'

        ]);

        $presentation->update($request->all());

        return response()->json($presentation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presentation $presentation)
    {
        $presentation->delete();

        return response()->json($presentation);
    }
}
