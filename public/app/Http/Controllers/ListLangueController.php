<?php

namespace App\Http\Controllers;

use App\Helpers\ModelUtils;
use App\Models\ListLangue;
use Illuminate\Http\Request;

class ListLangueController extends Controller
{
    public function getLangue()
    {
        $langues = ModelUtils::getIdIndexedAndTranslated(new ListLangue(), 'llangue_name', true, true);

        return response()->json($langues, 200);
    }
}
