<?php

namespace App\Http\Controllers;

use App\Helpers\ModelUtils;
use App\Models\ListLangue;
use App\Models\ListProfession;
use App\Models\Translation;
use Illuminate\Http\Request;

/**
 * Gets all professions paginated
 * @param String languageISO
 * @param String profession_name
 * @return LaravelPaginated
 */
class ListProfessionController extends Controller
{
    public function getProfessions(Request $request)
    {
        $professional = ListProfession::leftJoin('translations AS t', function ($join)
        {
            $join->on('listprofessions.profession_name', '=', 't.en');
        })->leftJoin('jobslist AS jl', function ($join)
        {
            $join->on('listprofessions.lprofession_id', '=', 'jl.profession_for_job');
        })
            ->where('listprofessions.valid_profession', '=', '1')
            ->groupBy('listprofessions.lprofession_id', 't.en', 't.pt', 't.es', 'listprofessions.parent_profession_id', 'listprofessions.valid_profession', 'listprofessions.person_id', 'listprofessions.profession_category_id');

        if ($request->has('profession_name'))
        {
            $languageISO = $request->input('languageISO', Translation::OFFICIAL_LANGUAGES[0]);

            if (!in_array($languageISO, Translation::OFFICIAL_LANGUAGES))
            {
                $languageISO = Translation::OFFICIAL_LANGUAGES[0];
            }

            $professional->where('t.' . $languageISO, 'like', '%' . $request->input('profession_name') . '%');
        }

        $professional = $professional
            ->selectRaw('
            listprofessions.lprofession_id,
            t.en,
            t.pt,
            t.es,
            listprofessions.parent_profession_id,
            listprofessions.valid_profession,
            listprofessions.person_id,
            listprofessions.profession_category_id,
            COUNT(jl.job_id) AS job_count
        ')
            ->orderBy('job_count', 'DESC')
            ->paginate(request('per_page', 20));

        return response()->json($professional);
    }

    /**
     * List all professions
     * @return \Illuminate\Http\JsonResponse 
     */
    public function list()
    {
        returnResponse(ModelUtils::getTranslationsArray(new ListProfession(), 'profession_name', null, null, (new ListLangue())->getNotOficialLangsIso()), 200);
    }
}
