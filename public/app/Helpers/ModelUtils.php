<?php

namespace App\Helpers;

use App\Models\Translation;

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
                        $unofficial = json_decode($thisObject->unofficial_translations, true);
                        if(is_array($unofficial)){
                            foreach($unofficial as $languageIso => $translatedText){
                                $thisData[$languageIso] = $translatedText;
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
     * @return Array schema [
     *      $objectId => $object
     * ]
     */
    public static function getIdIndexedAndTranslated(Object $object, $relationAttr = '')
    {
        try {
            $foundObjectArray = $object::leftJoin('translations', function($join) use($object, $relationAttr){
                $join->on('translations.en', $object->getTable() . '.' . $relationAttr);
            })->get();
            $data = [];
            $attributes = array_merge($object->getFillable(), (new Translation())->getFillable());
            foreach($foundObjectArray as $objectData){
                $ranInstance = self::makeInstance($object);
                foreach($attributes as $attributeName){
                    $ranInstance->{$attributeName} = $objectData->{$attributeName};
                }
                $data[$objectData->{$object->getKeyName()}] = $ranInstance;
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
}
