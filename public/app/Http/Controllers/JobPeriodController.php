<?php

namespace App\Http\Controllers;

use App\Helpers\ModelUtils;
use App\Models\JobPeriod;
use App\Models\ListLangue;

class JobPeriodController extends Controller
{
    /**
     * List all job contracts
     * @param Stirng currency
     * @return \Illuminate\Http\JsonResponse 
     */
    public function index()
    {
        returnResponse(ModelUtils::getTranslationsArray(new JobPeriod(), 'name', null, null, (new ListLangue())->getNotOficialLangsIso()), 200);
    }
}
