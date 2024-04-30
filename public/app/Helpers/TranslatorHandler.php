<?php

namespace App\Helpers;

class TranslatorHandler
{
    const PATH_TO_FILE = 'app/translations/cachedTranslations.json';
    
    /**
     * Cached translations file: It is dynamic and the base key is "en" so, all translated languages will be included inside the key 
     * and it can be composed of many different languages iso code or none
     * @return Array
     */
    private static $cachedFile;
    private static $newTranslation;

    /**
     * Fetches cached translation file, if sent text is not on file, translate it and save it on file before returning the translation
     * @param Stirng text - required
     * @param String languageIso
     * @param Bool save - Persist the translation in cached translation file
     * @return String|Bool 
     */
    public static function translate($text = '', $languageIso = 'en', $save = true)
    {
        if(!$text)
            return false;
        $cachedTranslations = !self::$cachedFile ? self::getFile() : self::$cachedFile;
        if(array_key_exists($text, $cachedTranslations) && (is_array($cachedTranslations[$text]) && array_key_exists($languageIso, $cachedTranslations[$text]) && $cachedTranslations[$text][$languageIso]))
            return $cachedTranslations[$text][$languageIso];
        $translation = self::fetchTranslationInGoogle($text, $languageIso);
        if(!$translation)
            return false;
        self::$newTranslation = true;
        if($save){
            $cachedTranslations[$text][$languageIso] = $translation;
            self::saveFile($cachedTranslations);
        }
        return $translation;
    }

    /**
     * Attempt the translation of sent array of strings and updates cached trasnlation file if new translations were added
     * @param Array textArray - required
     * @param String languageIso
     * @return Array|Bool 
     */
    public static function translateAll($textArray = [], $languageIso = 'en')
    {
        if(empty($textArray))
            return false;
        self::$newTranslation = false;
        self::$cachedFile = self::getFile();
        $fileCopy = self::$cachedFile;
        foreach($textArray as $text){
            $fileCopy[$text][$languageIso] = self::translate($text, $languageIso, false);
        }
        if(self::$newTranslation)
            self::saveFile($fileCopy);
        return self::getFile();
    }

    /**
     * Fetches cached translations file
     * Obs: The cached translations file may or may not contain all language iso in the file, possible undefined, ALWAYS CHECK!
     * @return Array - Schema: ['text' => [$anotherLanguageIso => $itsTranslation]]
     */
    public static function getFile()
    {
        $fullPath = storage_path(TranslatorHandler::PATH_TO_FILE);
        if(!file_exists($fullPath)){
            mkdir(storage_path('app/translations'));
            file_put_contents($fullPath, json_encode([]));
        }
        return json_decode(file_get_contents($fullPath), true);
    }

    /**
     * Updates cached translations file
     * @param Array - Schema: ['text' => [$anotherLanguageIso => $itsTranslation]]
     * @return Bool
     */
    public static function saveFile($data = [])
    {
        $fullPath = storage_path(TranslatorHandler::PATH_TO_FILE);
        if(!file_exists($fullPath))
            return false;
        file_put_contents($fullPath, json_encode($data));
        return true;
    }

    /**
     * Translate sent text with Google Translator service
     * @param String text - always in english
     * @param String targetLanguageIso
     * @return String translated text
     */
    public static function fetchTranslationInGoogle($text = '', $targetLanguageIso = 'en')
    {
        if($targetLanguageIso == 'en' || !$text)
            return $text;
        $tr = new \Stichoza\GoogleTranslate\GoogleTranslate();
        $tr->setSource('en');
        $tr->setTarget($targetLanguageIso);
        return $tr->translate($text);
    }
}