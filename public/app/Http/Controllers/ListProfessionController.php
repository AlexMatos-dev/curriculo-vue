<?php

namespace App\Http\Controllers;

use App\Models\ListProfession;
use App\Models\Translation;
use Illuminate\Http\Request;

class ListProfessionController extends Controller
{
    public function getProfessions(Request $request)
    {
        $professional = ListProfession::leftJoin('translations AS t', function ($join)
        {
            $join->on('listprofessions.profession_name', '=', 't.en');
        })
            ->where('listprofessions.valid_profession', '=', '1');

        if ($request->has('profession_name'))
        {
            $languageISO = $request->input('languageISO', Translation::OFFICIAL_LANGUAGES[0]);

            if (!in_array($languageISO, Translation::OFFICIAL_LANGUAGES))
            {
                $languageISO = Translation::OFFICIAL_LANGUAGES[0];
            }

            $professional->where('t.' . $languageISO, 'like', '%' . $request->input('profession_name') . '%');
        }

        $professional = $professional->select('listprofessions.*')
            ->groupBy('listprofessions.lprofession_id', 'listprofessions.profession_name', 'listprofessions.valid_profession', 't.en', 't.pt', 't.es');

        return response()->json($professional->get(), 200);
    }
}
