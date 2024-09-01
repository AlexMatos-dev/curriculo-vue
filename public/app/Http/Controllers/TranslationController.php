<?php

namespace App\Http\Controllers;

use App\Helpers\WebResponse;
use App\Models\ListLangue;
use Illuminate\Support\Facades\Artisan;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationController extends Controller
{
    public function systemTranslationView()
    {
        return view('systemTranslations');
    }

    /**
     * Tries to add new translations to systemTranslations
     * @param String translations
     */
    public function updateSystemTranslations()
    {
        $file = $this->request->file('translations');
        if(!$file || $file->getMimeType() != 'application/json')
            WebResponse::returnJson(translate('invalid file format. Only send JSON files'));
        if(!file($file->path()))
            WebResponse::returnJson(translate('file not found. Try sending file again'));
        $newTranslations = json_decode(file_get_contents($file->path()), true);
        if(!$newTranslations || !is_array($newTranslations))
            WebResponse::returnJson(translate('invalid file. Can not read it'));
        $translatorPt = new GoogleTranslate('pt', 'en');
        $translatorEs = new GoogleTranslate('es', 'en');
        $path = storage_path('app/dbSourceFiles/systemTranslations.json');
        if(!file_exists($path))
            file_put_contents($path, json_encode([]));
        $data = json_decode(file_get_contents($path), true);
        if(!$data || !is_array($data))
            $data = [];
        $existingEnKeys = [];
        if(count($data) > 0){
            foreach($data as $trans){
                $existingEnKeys[] = $trans['en'];
            }
        }
        $hasNew = false;
        $unoficial = (new ListLangue())->getNotOficialLangsIso();
        try {
            foreach($newTranslations as $translations){
                if(!$translations || !is_array($translations) || !array_key_exists('en', $translations))
                    continue;
                if(in_array($translations['en'], $existingEnKeys))
                    continue;
                $pt = array_key_exists('pt', $translations) && $translations['pt'] ? $translations['pt'] : $translatorPt->translate($translations['en']);
                $es = array_key_exists('es', $translations) && $translations['es'] ? $translations['es'] : $translatorEs->translate($translations['en']);
                $unoficialLangs = [];
                foreach($unoficial as $isoCode => $val){
                    $unoficialLangs[$isoCode] = null;
                }
                $data[] = [
                    'en' => $translations['en'],
                    'pt' => $pt,
                    'es' => $es,
                    'unoficialTranslations' => $unoficialLangs
                ];
                $hasNew = true;
            }
        } catch (\Throwable $th) {
            WebResponse::returnJson(translate('file content not read. Check your file and the correct schema: [en => $enTrans, pt => $ptTrans, es => $esTras]'));
        }
        $message = translate('method executed');
        if($hasNew){
            file_put_contents($path, json_encode($data));
            $message = translate('translations file updated');
            Artisan::call('db:seed --class=SystemTranslationSeeder');
        }
        WebResponse::returnJson($message, true);
    }
}
