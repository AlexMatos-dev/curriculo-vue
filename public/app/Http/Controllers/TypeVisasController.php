<?php

namespace App\Http\Controllers;

use App\Models\TypeVisas;
use Illuminate\Http\Request;

class TypeVisasController extends Controller
{
    public function index()
    {
        $typeVisas = TypeVisas::leftJoin('translations AS t', function ($join)
        {
            $join->on('type_visas.type_name', '=', 't.en');
        });

        return response()->json($typeVisas->get(), 200);
    }
}
