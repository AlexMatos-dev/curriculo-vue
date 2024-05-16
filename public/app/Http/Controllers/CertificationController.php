<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\Certification;
use Illuminate\Http\Request;

class CertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Int cercurriculum_id
     * @param Int per_page - default 10
     */
    public function index(Request $request)
    {
        $certifications = Certification::where('cercurriculum_id', $request->cercurriculum_id)->paginate(request('per_page', 10));
        return response()->json($certifications);
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
            'certification_name'        => 'required|max:150',
            'cerissuing_organization'   => 'required|max:200',
            'cerissue_date'             => 'required|date_format:Y-m-d|before:now',
            'cert_hours'                => 'required|integer',
            'cerdescription'            => 'max:500',
            'cerlink'                   => 'max:100|url'
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
    public function update(Request $request, $certificationId = null)
    {
        $certification = Certification::where('curriculums.cprofes_id', $this->getProfessionalBySession()->professional_id)->where('certifi_id', $certificationId)
        ->leftJoin('curriculums', function($join){
            $join->on('curriculums.curriculum_id', '=', 'certifications.cercurriculum_id');
        })->first();
        if(!$certification)
            Validator::throwResponse('certification not found', 400);
        $request->validate([
            'certification_name'        => 'required|max:150',
            'cerissuing_organization'   => 'required|max:200',
            'cerissue_date'             => 'required|date_format:Y-m-d|before:now',
            'cert_hours'                => 'required|integer',
            'cerdescription'            => 'max:500',
            'cerlink'                   => 'max:100|url'
        ]);
        $certification->update($request->all());

        return response()->json($certification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($certificationId)
    {
        $certification = Certification::where('curriculums.cprofes_id', $this->getProfessionalBySession()->professional_id)->where('certifi_id', $certificationId)
        ->leftJoin('curriculums', function($join){
            $join->on('curriculums.curriculum_id', '=', 'certifications.cercurriculum_id');
        })->first();
        if(!$certification)
            Validator::throwResponse('certification not found', 400);
        $certification->delete();

        return response()->json($certification);
    }
}
