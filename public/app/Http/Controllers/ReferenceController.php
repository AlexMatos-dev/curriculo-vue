<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use App\Models\Curriculum;

use Illuminate\Http\Request;

class ReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $curriculum = Curriculum::where('curriculum_id', $request->curriculum_id)->first();

        if($curriculum){
            $reference = Reference::where('refcurriculum_id',$curriculum->curriculum_id );
            $reference =  $reference->paginate($request->pere_page);
            return response()->json($reference);
        }
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
        'refcurriculum_id'  =>'required',
        'reference_name'    =>'required',
        'reference_email'   =>'required',
        'refrelationship'   =>'required'
        ]);

        $reference = Reference::create($request->all());

        return response()->json($reference);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reference $reference)
    {

        return response()->json($reference);
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
    public function update(Request $request, Reference $reference)
    {
        $request->validate([
            'refcurriculum_id'  =>'required',
            'reference_name'    =>'required',
            'reference_email'   =>'required',
            'refrelationship'   =>'required'
        ]);

        $reference->update($request->all());

        return response()->json($reference);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reference $reference)
    {
        $reference->delete();

        return response()->json($reference);
    }
}
