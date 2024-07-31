<?php

namespace App\Http\Controllers;

use App\Helpers\ModelUtils;
use App\Models\DrivingLicense;
use App\Models\ListLangue;

class DrivingLicenseController extends Controller
{
    /**
     * List all driving licenses
     * @return \Illuminate\Http\JsonResponse 
     */
    public function index()
    {
        returnResponse(ModelUtils::getTranslationsArray(new DrivingLicense(), 'name', null, null, (new ListLangue())->getNotOficialLangsIso()), 200);
    }
}
