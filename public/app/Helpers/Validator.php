<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class Validator
{
    /**
     * Performes a validation on Request parameters accordingly to sent rules. In case of trigger on validation fail, does not throw exception, 
     * just returns a JSON response with correspondent code
     * @return Bool|\Illuminate\Http\JsonResponse
     */
    public static function validateParameters(Request $request, $rules = [])
    {
        try {
            $request->validate($rules);
        } catch (\Throwable $th) {
            response()->json(['message' => $th->getMessage()], 400)->send();
            die();
        }
        return true;
    }

    /**
     * Performes a validation on sent image in binary and checks its format and size (max allowed = 2mb). 
     * @param Binary image
     * @return Bool|\Illuminate\Http\JsonResponse
     */
    public static function validateImage($image = null)
    {
        if(!$image){
            response()->json(['message' => 'invalid image'], 400)->send();
            die();
        }
        $fileHandler = new FileHandler($image);
        if(!$fileHandler->isExtensionValid()){
            response()->json(['message' => 'invalid image format'], 400);
            die();
        }
        if(!$fileHandler->isFileSizeValid()){
            response()->json(['message' => 'image too big, max of 2 mb allowed'], 400);
            die();
        }
        return $fileHandler;
    }

    public static function validateCNPJ($cnpj = null)
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
        if (strlen($cnpj) != 14)
            return false;
        for ($i = 0, $j = 5, $sum = 0; $i < 12; $i++){
            $sum += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $value = $sum % 11;
        if ($cnpj[12] != ($value < 2 ? 0 : 11 - $value))
            return false;
        for ($i = 0, $j = 6, $sum = 0; $i < 13; $i++){
            $sum += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $value = $sum % 11;
        $result = $cnpj[13] == ($value < 2 ? 0 : 11 - $value);
        if(!$result)
            response()->json(['message' => 'cnpj is invalid'], 400);
        return true;
    }

    /**
     * Performes a validation sent array, checking by a query if id exists on table.
     * Obs: may not return an object if sent 'data' key is null or empty, always check if $attrName exists in return Array
     * @param Array - Schema: [$attrName => ['object'=> $objectPath , 'data' => $objectId]] 
     * @return Array [$attrName => $objectInstance]
     */
    public static function checkExistanceOnTable($dataToCheck = [])
    {
        $objects = [];
        foreach($dataToCheck as $key => $data){
            if(!$data['data'])
                continue;
            if(!array_key_exists('object', $data) || !array_key_exists('data', $data)){
                response()->json(['message' => 'invalid parameters for validation'], 500)->send();
                die();
            }
            try {
                $objInstance = new $data['object']();
                $foundObject = $objInstance->find($data['data']);
            } catch (\Throwable $th) {
                response()->json(['message' => "$key is not valid"], 400)->send();
                die();
            }
            if(!$foundObject){
                response()->json(['message' => 'invalid parameters for validation'], 500)->send();
                die();
            }
            $objects[$key] = $foundObject;
        }
        return $objects;
    }

    /**
     * Returns Person password rule schema (An uppercase and lowecase character, a number, a special character and more than 6 character length)
     */
    public static function getPersonPasswordRule()
    {
        return [
            'required',
            'min:6',              // must contain more than 5 characters
            'regex:/[a-z]/',      // must contain at least one lowercase letter
            'regex:/[A-Z]/',      // must contain at least one uppercase letter
            'regex:/[0-9]/',      // must contain at least one digit
            'regex:/[@$!%*#?&]/', // must contain a special character
            'max:80'              // must contain less than 81 characters
        ];
    }
}