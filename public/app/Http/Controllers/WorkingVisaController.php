<?php

namespace App\Http\Controllers;

use App\Helpers\ModelUtils;
use App\Models\ListLangue;
use App\Models\WorkingVisa;

class WorkingVisaController extends Controller
{
    /**
     * List all job contracts
     * @param Stirng currency
     * @return \Illuminate\Http\JsonResponse 
     */
    public function index()
    {
        returnResponse(ModelUtils::getTranslationsArray(new WorkingVisa(), 'name', null, null, (new ListLangue())->getNotOficialLangsIso()), 200);
    }
}
