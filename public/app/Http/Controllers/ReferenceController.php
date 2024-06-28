<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\Reference;
use App\Models\Curriculum;

use Illuminate\Http\Request;

class ReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Int refcurriculum_id
     * @param Int per_page
     */
    public function index(Request $request)
    {
        $reference = Reference::where('refcurriculum_id', $request->refcurriculum_id)->paginate(request('per_page', 10));
        returnResponse($reference);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'refcurriculum_id'  =>'required|integer',
            'reference_name'    =>'required',
            'reference_email'   =>'required',
            'refrelationship'   =>'required'
        ]);
        $reference = Reference::create($request->all());

        returnResponse($reference);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reference $reference)
    {

        returnResponse($reference);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reference $reference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $referenceId = null)
    {
        $reference = Reference::where('curriculums.cprofes_id', $this->getProfessionalBySession()->professional_id)->where('reference_id', $referenceId)
        ->leftJoin('curriculums', function($join){
            $join->on('curriculums.curriculum_id', '=', 'references.refcurriculum_id');
        })->first();
        if(!$reference)
            Validator::throwResponse(translate('reference not found'), 400);
        $request->validate([
            'reference_name'    =>'required',
            'reference_email'   =>'required',
            'refrelationship'   =>'required'
        ]);
        $reference->update($request->all());

        returnResponse($reference);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($referenceId)
    {
        $reference = Reference::where('curriculums.cprofes_id', $this->getProfessionalBySession()->professional_id)->where('reference_id', $referenceId)
        ->leftJoin('curriculums', function($join){
            $join->on('curriculums.curriculum_id', '=', 'references.refcurriculum_id');
        })->first();
        if(!$reference)
            Validator::throwResponse(translate('reference not found'), 400);
        $reference->delete();
        returnResponse($reference);
    }
}
