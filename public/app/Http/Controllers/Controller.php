<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Controller
{
    public $request;
    public function __construct(Request $requestObj)
    {
        $this->request = $requestObj;
    }

    public function getCompanyBySession()
    {
        $companyObj = Session()->get('company');
        if(!$companyObj)
            return false;
        return $companyObj;
    }

    public function getProfessionalBySession()
    {
        $professionalObj = Session()->get('professional');
        if(!$professionalObj)
            return false;
        return $professionalObj;
    }

    public function getCurriculumBySession()
    {
        $curriculumObj = Session()->get('curriculum');
        if(!$curriculumObj)
            return false;
        return $curriculumObj;
    }
}
