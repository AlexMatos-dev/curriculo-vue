<?php

namespace App\Http\Controllers;

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
            $data = getSystemTranslations();
            $filtered = [];
            foreach($data as $en => $translations){
                unset($translations['object']);
                $filtered[$en] = $translations;
            }
            $json = json_encode($filtered, JSON_PRETTY_PRINT);
            file_put_contents($path, $json);
            returnResponse(['message' => 'done']);
        } catch (\Throwable $th) {
            Log::alert('Export translations failed: ' . $th->getMessage());
            returnResponse(['message' => $th->getMessage()], 500);
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
            file_put_contents($path, json_encode($result, JSON_PRETTY_PRINT));
            returnResponse(['message' => 'done']);
        } catch (\Throwable $th) {
            Log::alert('Export languages failed: ' . $th->getMessage());
            returnResponse(['message' => $th->getMessage()], 500);
        }
    }
}
