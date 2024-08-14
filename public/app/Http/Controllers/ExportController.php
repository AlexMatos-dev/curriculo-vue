<?php

namespace App\Http\Controllers;

use App\Helpers\ModelUtils;
use App\Models\CommonCurrency;
use App\Models\ListCountry;
use App\Models\ListLangue;
use App\Models\Translation;
use Illuminate\Support\Facades\Log;

class ExportController extends Controller
{
    /**
     * Exports system translations
     */
    function exportTranslations()
    {
        if(!is_dir(storage_path('app/exports')))
            mkdir(storage_path('app/exports'));
        $path = storage_path('app/exports/translations.json');
        try {
            $data = getSystemTranslations(true);
            $filtered = [];
            foreach($data as $en => $translations){
                unset($translations['object']);
                $filtered[$en] = $translations;
            }
            $json = json_encode($filtered);
            file_put_contents($path, $json);
            exit(json_encode(['message' => 'done']));
        } catch (\Throwable $th) {
            Log::alert('Export translations failed: ' . $th->getMessage());
            exit(json_encode(['message' => 'Erro: ' . $th->getMessage()]));
        }
    }

    /**
     * Exports system languages
     */
    function exportLanguages()
    {
        if(!is_dir(storage_path('app/exports')))
            mkdir(storage_path('app/exports'));
        $path = storage_path('app/exports/languages.json');
        $allIsoCodes = ListLangue::all();
        $isoCodes = [];
        foreach($allIsoCodes as $lang){
            $isoCodes[] = $lang->llangue_acronyn;
        }
        $languages = ListLangue::leftJoin('translations AS translation', function($join){
            $join->on('listlangues.llangue_name', '=', 'translation.en');
        })->get();
        $result = [];
        foreach($languages as $language){
            $data = [];
            $unoficial = $language->unofficial_translations ? json_decode($language->unofficial_translations, true) : [];
            foreach($isoCodes as $isoCode){
                if(in_array($isoCode, Translation::OFFICIAL_LANGUAGES)){
                    $data[$isoCode] = $language->{$isoCode};
                    continue;
                }
                $data[$isoCode] = array_key_exists($isoCode, $unoficial) ? $unoficial[$isoCode] : null; 
            }
            $data['llangue_id'] = $language->llangue_id;
            $result[$language->llangue_acronyn] = $data;
        }
        try {
            file_put_contents($path, json_encode($result));
            exit(json_encode(['message' => 'done']));
        } catch (\Throwable $th) {
            Log::alert('Export languages failed: ' . $th->getMessage());
            exit(json_encode(['message' => 'Erro: ' . $th->getMessage()]));
        }
    }

    /**
     * Exports system common currency
     */
    function exportCommonCurrencies()
    {
        if(!is_dir(storage_path('app/exports')))
            mkdir(storage_path('app/exports'));
        $path = storage_path('app/exports/currencies.json');
        $data = ModelUtils::getTranslationsArray(
            new CommonCurrency(), 'currency_name', [], null, (new ListLangue())->getNotOficialLangsIso()
        );
        $result = [];
        foreach($data as $values){
            $result[] = $values;
        }
        try {
            file_put_contents($path, json_encode($result));
            exit(json_encode(['message' => 'done']));
        } catch (\Throwable $th) {
            Log::alert('Export languages failed: ' . $th->getMessage());
            exit(json_encode(['message' => 'Erro: ' . $th->getMessage()]));
        }
    }

    /**
     * Exports system common currency
     */
    function exportCountries()
    {
        if(!is_dir(storage_path('app/exports')))
            mkdir(storage_path('app/exports'));
        $path = storage_path('app/exports/countries.json');
        $data = ModelUtils::getTranslationsArray(
            new ListCountry(), 'lcountry_name', [], null, (new ListLangue())->getNotOficialLangsIso()
        );
        $result = [];
        foreach($data as $values){
            $result[] = $values;
        }
        try {
            file_put_contents($path, json_encode($result));
            exit(json_encode(['message' => 'done']));
        } catch (\Throwable $th) {
            Log::alert('Export languages failed: ' . $th->getMessage());
            exit(json_encode(['message' => 'Erro: ' . $th->getMessage()]));
        }
    }
}
