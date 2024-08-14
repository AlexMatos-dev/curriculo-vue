<?php

namespace App\Http\Controllers;

use App\Helpers\ModelUtils;
use App\Helpers\Validator;
use App\Models\CertificationType;
use App\Models\JobCertification;
use App\Models\ListLangue;

class JobCertificationController extends Controller
{
    /**
     * List all certifications
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

    /**
     * Searches for Certifications accordingly to sent word and language
     * @param String term
     * @param Int limit - default = 5
     * @return @return \Illuminate\Http\JsonResponse
     */
    public function search()
    {
        Validator::validateParameters($this->request, [
            'limit' => 'integer'
        ]);
        $limit = request('limit', 5);
        if($limit > 50)
            $limit = 50;
        if(!request('term'))
            returnResponse(['data' => []]);
        $results = (new CertificationType())->getCertificationTypeByNameAndLanguage((string)request('term', ''), $limit);
        if(count($results) > 0)
            $results = ModelUtils::parseAsArrayWithAllLanguagesIsosAndTranslations($results, ['certification_type']);
        returnResponse(['data' => $results]);
    }
}
