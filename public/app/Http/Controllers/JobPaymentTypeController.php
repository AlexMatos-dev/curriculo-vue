<?php

namespace App\Http\Controllers;

use App\Helpers\ModelUtils;
use App\Models\JobPaymentType;
use App\Models\ListLangue;

class JobPaymentTypeController extends Controller
{
    /**
     * List all payment types
     * @param Stirng currency
     * @return \Illuminate\Http\JsonResponse 
     */
    public function index()
    {
        returnResponse(ModelUtils::getTranslationsArray(new JobPaymentType(), 'name', null, null, (new ListLangue())->getNotOficialLangsIso()), 200);
    }
}
