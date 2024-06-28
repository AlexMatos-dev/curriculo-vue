<?php

namespace App\Http\Controllers;

use App\Models\CommonCurrency;

class CommonCurrencyController extends Controller
{
    /**
     * List all common currencies
     * @param Stirng currency
     * @return \Illuminate\Http\JsonResponse 
     */
    public function index()
    {
        $commonCurrency = CommonCurrency::leftJoin('translations', function ($join){
            $join->on('common_currencies.currency_name', '=', 'translations.en');
        })->select('common_currencies.*', 'translations.en', 'translations.pt', 'translations.es', 'translations.unofficial_translations');
        if(request('currency'))
            $commonCurrency->where('common_currencies.currency', mb_strtoupper(request('currency')));
        returnResponse($commonCurrency->get(), 200);
    }
}
