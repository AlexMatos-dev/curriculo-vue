<?php

namespace App\Http\Controllers;

use App\Models\CompanyType;
use App\Models\Translation;
use Illuminate\Http\Request;

class CompanyTypeController extends Controller
{
    /**
     * List all company types
     * @param String name
     * @param String languageISO - One of the official languages, default is EN
     * @return \Illuminate\Http\JsonResponse 
     */
    public function getCompanyTypes()
    {
        $companyTypes = CompanyType::leftJoin('translations AS t', function ($join)
        {
            $join->on('company_types.name', '=', 't.en');
        })->select(
            'company_types.company_type_id',
            't.en',
            't.pt',
            't.es',
            't.unofficial_translations',
            'company_types.created_at',
            'company_types.updated_at'
        );
        if(request('name')){
            $languageISO = request('languageISO', Translation::OFFICIAL_LANGUAGES[0]);
            if(!in_array($languageISO, Translation::OFFICIAL_LANGUAGES))
                $languageISO = Translation::OFFICIAL_LANGUAGES[0];
            $companyTypes->where($languageISO, 'like', '%'.request('name').'%');
        }
        returnResponse($companyTypes->get(), 200);
    }
}
