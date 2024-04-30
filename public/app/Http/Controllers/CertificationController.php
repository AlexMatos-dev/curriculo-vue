<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Curriculum;
use Illuminate\Http\Request;

class CertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $curriculum = Curriculum::where('curriculum_id', $request->curriculum_id)->first();

        if($curriculum){
            $certification = Certification::where('cercurriculum_id',$curriculum->curriculum_id );
            $certification =  $certification->paginate($request->pere_page);
            return response()->json($certification);
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
            'cercurriculum_id'          => 'required',
            'certification_name'        => 'required',
            'cerissuing_organization'   => 'required',
            'cerissue_date'             => 'required',
            'cert_hours'                => 'required',
            'cerdescription'            => 'required',
            'cerlink'                   => 'required'
    ]);

        $certification = Certification::create($request->all());

        return response()->json($certification);
    }

    /**
     * Display the specified resource.
     */
    public function show(Certification $certification)
    {

       return response()->json($certification);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Certification $certification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Certification $certification)
    {
        $request->validate([
            'certification_name'        => 'required',
            'cerissuing_organization'   => 'required',
            'cerissue_date'             => 'required',
            'cert_hours'                => 'required',
            'cerdescription'            => 'required',
            'cerlink'                   => 'required'
        ]);

        $certification->update($request->all());

        return response()->json($certification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certification $certification)
    {
        $certification->delete();

        return response()->json($certification);
    }
}
