<?php

namespace App\Http\Controllers;

use App\Helpers\ModelUtils;
use App\Models\JobCertification;
use App\Models\ListLangue;

class JobCertificationController extends Controller
{
    /**
     * List all driving licenses
     * @return \Illuminate\Http\JsonResponse 
     */
    public function index()
    {
        $customQuery = JobCertification::select('*')->leftJoin('certification_types', function($join){
            $join->on('certification_types.certification_type', 'job_certifications.certification_type');
        })->leftJoin('translations', function($join){
            $join->on("translations.en",  'certification_types.name');
        })->distinct();
        returnResponse(ModelUtils::getTranslationsArray(
            new JobCertification(), 'certification_type', null, null, (new ListLangue())->getNotOficialLangsIso(), false, true, $customQuery
        ), 200);
    }
}
