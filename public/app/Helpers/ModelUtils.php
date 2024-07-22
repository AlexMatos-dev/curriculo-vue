<?php

namespace App\Helpers;

use App\Models\ListLangue;
use App\Models\Translation;
use Carbon\Carbon;
use ReflectionClass;

class ModelUtils
{
    const BASE_MODELS_PATH = 'App\Models';

    /**
     * Returns an array of sent $object attributes from $fillable property
     * @param Object $object
     * @param Bool addId - default = false
     * @param Array findAndMerge - in order to find ian a object array list the expected id and set it: schema: ['objects' => [], 'translated' => false, 'to' => '']
     * @return Array of object attributes 
     */
    public static function getFillableData(Object $object, $addId = false, $findAndMerge = [])
    {
        $languages = ListLangue::whereNotIn('llangue_acronyn', Translation::OFFICIAL_LANGUAGES)->get();
        try {
            $attributesNames = $object->getFillable();
            if($addId)
                $attributesNames[] = $object->getKeyName();
            $data = [];
            $mergeKeys = array_keys($findAndMerge);
            foreach($attributesNames as $attributeName){
                $data[$attributeName] = $object->{$attributeName};
            }
            if(!empty($mergeKeys)){
                foreach($findAndMerge as $keyName => $info){
                    if(!array_key_exists($keyName, $data) || !array_key_exists($data[$keyName], $info['objects']))
                        continue;
                    $thisData = self::getFillableData($info['objects'][$data[$keyName]]);
                    if(array_key_exists('translated', $info) && $info['translated']){
                        $thisObject = $info['objects'][$data[$keyName]];
                        $thisData['en'] = $thisObject->en;
                        $thisData['pt'] = $thisObject->pt;
                        $thisData['es'] = $thisObject->es;
                        $unofficial = $thisObject->unofficial_translations ? json_decode($thisObject->unofficial_translations, true) : [];
                        foreach($languages as $language){
                            $langIso = $language->llangue_acronyn;
                            $thisData[$langIso] = null;
                            if(array_key_exists($langIso, $unofficial)){
                                $thisData[$langIso] = $unofficial[$langIso];
                            }
                        }
                    }
                    $to = array_key_exists('to', $info) ? $info['to'] : $keyName;
                    $data[$to] = $thisData;
                }
            }
            return $data;
        } catch (\Throwable $th) {
            return $object->toArray();
        }
    }

    /**
     * Returns an array with requested data and with all languages isos with or without translations filed
     * @param Array arrayOfObject
     * @param Array attrsToAdd - the custom attributes to set
     * @param String keyOfArray - if not sent will be the array index
     * @return Array of object attributes 
     */
    public static function parseAsArrayWithAllLanguagesIsosAndTranslations($arrayOfObject = [], $attrsToAdd = [], $keyOfArray = null)
    {
        $datas = [];
        $unofficialLangs = (new ListLangue())->getNotOficialLangsIso();
        $attrs = Translation::OFFICIAL_LANGUAGES;
        $attrs = array_merge($attrs, $attrsToAdd);
        foreach($arrayOfObject as $object){
            $data = [];
            foreach($attrs as $attributeName){
                $data[$attributeName] = $object->{$attributeName};
            }
            $unnoficialTranslations = $object->unofficial_translations ? json_decode($object->unofficial_translations, true) : [];
            foreach($unofficialLangs as $langIso => $langueObj){
                $data[$langIso] = array_key_exists($langIso, $unnoficialTranslations) ? $unnoficialTranslations[$langIso] : null;
            }
            if($keyOfArray){
                $datas[$object->{$keyOfArray}][] = $data;
            }else{
                $datas[] = $data;
            }
        }
        return $datas;
    }
    
    /**
     * Gets all sent $object as an array indexed by its id
     * @param Object $object
     * @param Bool fillableData - to return only fillable variables: default = false
     * @param Bool addIdToFillable - to add id to fillable variables: default = false
     * @return Array schema [
     *      $objectId => $object
     * ]
     */
    public static function getAllAsIdIndexedArray(Object $object, $fillableData = false, $addIdToFillable = false)
    {
        try {
            $id = $object->getKeyName();
            $data = [];
            foreach($object::all() as $objectInstance){
                $data[$objectInstance->{$id}] = $fillableData ? self::getFillableData($objectInstance, $addIdToFillable) : $objectInstance;
            }
            return $data;
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Gets all sent $object as an array indexed by its id or chosen attributw
     * @param Object $object
     * @param String attrToUse - default is the table id 
     * @return Array schema [
     *      $objectId => $object
     * ]
     */
    public static function getAllAsIndexedArray(Object $object, $attrToUse = '')
    {
        try {
            $id = !$attrToUse ? $object->getKeyName() : $attrToUse;
            $data = [];
            foreach($object::all() as $objectInstance){
                $data[$objectInstance->{$id}] = $objectInstance;
            }
            return $data;
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Gets all sent $object as an array indexed by its id and translated
     * @param Object $object
     * @param String relationAttr 
     * @param Bool noKey - default = false
     * @param Bool addId - default = false
     * @param Bool pureArray - default = false (parse data as array and not an object)
     * @return Array schema [
     *      $objectId => $object
     * ]
     */
    public static function getIdIndexedAndTranslated(Object $object, $relationAttr = '', $noKey = false, $addId = false, $pureArray = false)
    {
        $listLanguages = ListLangue::whereNotIn('llangue_acronyn', Translation::OFFICIAL_LANGUAGES)->get();
        try {
            $foundObjectArray = $object::leftJoin('translations', function($join) use($object, $relationAttr){
                $join->on('translations.en', $object->getTable() . '.' . $relationAttr);
            })->get();
            $data = [];
            $attributes = array_merge($object->getFillable(), (new Translation())->getFillable());
            foreach($foundObjectArray as $objectData){
                $rawInstance = self::makeInstance($object);
                foreach($attributes as $attributeName){
                    $rawInstance->{$attributeName} = $objectData->{$attributeName};
                }
                $unofficialTrans = $objectData->unofficial_translations ? json_decode($objectData->unofficial_translations, true) : [];
                foreach($listLanguages as $langIso){
                    $rawInstance->{$langIso->llangue_acronyn} = null;
                    if(array_key_exists($langIso->llangue_acronyn, $unofficialTrans))
                        $rawInstance->{$langIso->llangue_acronyn} = $unofficialTrans[$langIso->llangue_acronyn];
                }
                if($addId)
                    $rawInstance->{$objectData->getKeyName()} = $objectData->{$objectData->getKeyName()};
                if($noKey){
                    $data[] = $rawInstance;
                }else{
                    $data[$objectData->{$object->getKeyName()}] = $pureArray ? $rawInstance->toArray() : $rawInstance;
                }
            }
            return $data;
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Returns all languages iso + translations table data accordingly to sent instance related by parameters
     * @param Object object
     * @param String relationAttr - to relate to translations table
     * @param Array dataIds - only sent if wnats to filter the results
     * @param String attrName - to set the attr name to use the dataIds parameter
     * @param Array languagesToInsert - the languages to insert besides the oficial
     * @param Bool asObject - to return value as object
     * @return Array
     */
    public static function getTranslationsArray(Object $object, $relationAttr = '', $dataIds = [], $attrName = '', $languagesToInsert = [], $asObject = false)
    {
        try {
            $query = $object::leftJoin('translations', function($join) use($object, $relationAttr){
                $join->on('translations.en', $object->getTable() . '.' . $relationAttr);
            });
            if($dataIds || !empty($dataIds))
                $query->whereIn($attrName, $dataIds);
            $foundObjectArray = $query->get();
            $data = [];
            $attributes = array_merge($object->getFillable(), (new Translation())->getFillable());
            foreach($foundObjectArray as $objectData){
                $rawInstance = self::makeInstance($object);
                foreach($attributes as $attributeName){
                    $rawInstance->{$attributeName} = $objectData->{$attributeName};
                }
                $unofficialTrans = $objectData->unofficial_translations ? json_decode($objectData->unofficial_translations, true) : [];
                foreach($languagesToInsert as $langIso){
                    $rawInstance->{$langIso->llangue_acronyn} = null;
                    if(array_key_exists($langIso->llangue_acronyn, $unofficialTrans))
                        $rawInstance->{$langIso->llangue_acronyn} = $unofficialTrans[$langIso->llangue_acronyn];
                }
                unset($rawInstance->unofficial_translations);
                $rawInstance->object_id = $objectData->{$object->getKeyName()};
                $data[$objectData->{$object->getKeyName()}] = $asObject ? $rawInstance : $rawInstance->toArray();
            }
            return $data;
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Creates a new Instance of the same as sent $object
     * @param Object $object
     * @return Object the same type of $object
     */
    public static function makeInstance(Object $object)
    {
        $className = self::BASE_MODELS_PATH . trim('\ ') . class_basename($object);
        return new $className();
    }

    /**
     * Return all constants of sent Object
     * @param Object object
     * @return Array of constants
     */
    public static function getClassConstants(Object $object)
    {
        try {
            $oClass = new ReflectionClass($object);
            return $oClass->getConstants();
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Formats date accordingly to language
     * @param Date $data
     * @param Bool addHour - default = false
     * @param String userLang - default = ListLangue default language
     * @return String
     */
    public static function parseDateByLanguage($date, $addHour = false, $userLang = ListLangue::DEFAULT_LANGUAGE)
    {
        if(Session()->has('user_lang'))
            $userLang = Session()->get('user_lang');
        if(!in_array($userLang, Translation::OFFICIAL_LANGUAGES))
            $userLang = ListLangue::DEFAULT_LANGUAGE;
        if(is_string($date))
            $date = Carbon::parse($date);
        switch($userLang){
            case 'en':
                return $addHour ? $date->format('Y/m/d H:i:s') : $date->format('Y/m/d');
            break;
            case 'es':
                return $addHour ? $date->format('d/m/Y H:i:s') : $date->format('d/m/Y');
            break;
            case 'pt':
                return $addHour ? $date->format('d/m/Y H:i:s') : $date->format('d/m/Y');
            break;
            default:
                return $addHour ? $date->format('Y/m/d H:i:s') : $date->format('Y/m/d');
            break;
        }
    }
}
