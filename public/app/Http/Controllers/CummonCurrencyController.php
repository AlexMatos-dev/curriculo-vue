<?php

namespace App\Http\Controllers;

use App\Models\CummonCurrency;
use Illuminate\Http\Request;

class CummonCurrencyController extends Controller
{
    public function index()
    {
        $cummonCurrency = CummonCurrency::all();

        return response()->json($cummonCurrency, 200);
    }
}
