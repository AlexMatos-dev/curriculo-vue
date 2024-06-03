<?php

namespace App\Http\Controllers;

use App\Models\ListLangue;
use Illuminate\Http\Request;

class ListLangueController extends Controller
{
    public function getLangue()
    {
        $langue = ListLangue::all();

        return response()->json($langue, 200);
    }
}
