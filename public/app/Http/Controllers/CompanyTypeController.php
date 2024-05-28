<?php

namespace App\Http\Controllers;

use App\Models\CompanyType;
use Illuminate\Http\Request;

class CompanyTypeController extends Controller
{
    /**
     * List all company types
     * @param String company types
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

        return response()->json($companyTypes->get(), 200);
    }
}
