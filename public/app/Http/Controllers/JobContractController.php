<?php

namespace App\Http\Controllers;

use App\Helpers\ModelUtils;
use App\Models\JobContract;
use App\Models\ListLangue;

class JobContractController extends Controller
{
    /**
     * List all job contracts
     * @param Stirng currency
     * @return \Illuminate\Http\JsonResponse 
     */
    public function index()
    {
        returnResponse(ModelUtils::getTranslationsArray(new JobContract(), 'name', null, null, (new ListLangue())->getNotOficialLangsIso()), 200);
    }
}
