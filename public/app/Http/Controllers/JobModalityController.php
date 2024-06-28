<?php

namespace App\Http\Controllers;

use App\Models\JobModality;

class JobModalityController extends Controller
{
    public function index()
    {
        $jobsModalities = JobModality::leftJoin('translations AS t_name', function ($join)
        {
            $join->on('job_modalities.name', '=', 't_name.en');
        })->leftJoin('translations AS t_desc', function ($join)
        {
            $join->on('job_modalities.description', '=', 't_desc.en');
        })->select(
            'job_modalities.job_modality_id',
            't_name.en as en',
            't_name.pt as pt',
            't_name.es as es',
            't_desc.en as description_en',
            't_desc.pt as description_pt',
            't_desc.es as description_es',
            'job_modalities.created_at',
            'job_modalities.updated_at'
        );

        returnResponse($jobsModalities->get(), 200);
    }
}
