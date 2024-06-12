<?php

use App\Models\ListLangue;
use App\Models\Translation;

/**
 * Get translations from session or load them into it
 * @param Bool reload - to force a db call
 */
function getSystemTranslations($reload = false)
{
    if(!$reload){
        if(Session()->has('systemTranslations') && is_array(Session()->get('systemTranslations')))
            return Session()->get('systemTranslations', []);
    }
    $translations = (new Translation())->getAllTranslationsAsEnForIndex(Translation::CATEGORY_SYSTEM_TRANSLATIONS, true);
    Session()->put('systemTranslations', $translations);
    return Session()->get('systemTranslations', []);
}

/**
 * Fetch translations and attempts to translate text if translation is founded in Session user_lang or sent $enforcedLangIso
 * @param String text
 * @param String enforcedLangIso - default: null
 * @return String translatedText
 */
function translate($text, $enforcedLangIso = null)
{
    $translationObject = new Translation();
    $translations = getSystemTranslations();
    if(!array_key_exists($text, $translations)){
        $translationObject->fabricateTranslation($text, Translation::CATEGORY_SYSTEM_TRANSLATIONS);
        $translations = getSystemTranslations(true);
    }
    $langIso = $enforcedLangIso ? $enforcedLangIso : Session()->get('user_lang');
    if(!$langIso)
        $langIso = ListLangue::DEFAULT_LANGUAGE;
    if(!array_key_exists($text, $translations))
        return $text;
    $translatedText = $translations[$text]['en'];
    if(array_key_exists($langIso, $translations[$text]))
        $translatedText = $translations[$text][$langIso];
    return $translatedText;
}

/**
 * Fetch all trnaslations from code at 'translate()' methods and creates a Translations table reference
 */
function syncSystemTranslations(){
    $translationObject = new Translation();
    $persistedTranslations = ($translationObject)->getAllTranslationsAsEnForIndex(Translation::CATEGORY_SYSTEM_TRANSLATIONS, true);
    $basePath = __DIR__ . '/../';
    $dirs = [
        'Http/Controllers' => [],
        'Http/Middleware' => [],
        'Helpers' => ['SystemTranslator.php'],
        // 'Models' => [],
    ];
    $translations = [];
    foreach($dirs as $dir => $exceptions){
        $relativePath = $basePath . $dir;
        if(!is_dir($relativePath))
            continue;
        $files = scandir($relativePath);
        foreach($files as $file){
            if(in_array($file, ['.', '..']) || in_array($file, $exceptions))
                continue;
            $thisPath = "$relativePath/$file";
            if(!file_exists($thisPath))
                continue;
            $sourceFile = file_get_contents($thisPath);
            $array = explode('translate(', $sourceFile);
            unset($array[0]);
            foreach($array as $possibleTranslation){
                $methodMarker = $possibleTranslation[0];
                $textArray = explode($methodMarker, $possibleTranslation);
                if(!empty($textArray[1]) && !in_array($textArray[1], $translations) && !is_numeric(strpos($textArray[1], 'th->getMessage()')) && !array_key_exists($textArray[1], $persistedTranslations)){
                    $translations[] = $textArray[1];
                    $translationObject->fabricateTranslation($textArray[1], $translationObject::CATEGORY_SYSTEM_TRANSLATIONS);
                }else{
                    $used[] = $textArray[1];
                }
            }
        }
    }
}