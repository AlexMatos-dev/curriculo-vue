<?php

namespace App\Helpers;

use Carbon\Carbon;
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
            self::throwResponse(translate($th->getMessage()), 400);
        }
        return true;
    }

    /**
     * Performes a validation on Request parameters accordingly to sent rules, the rules are exclusive to comparing date. In case of trigger on validation fail, 
     * does not throw exception, just returns a JSON response with correspondent code
     * @return Bool|\Illuminate\Http\JsonResponse
     */
    public static function validateDates(Request $request, $rules = [])
    {
        $gathered = [];
        foreach($rules as $requestParam => $rule){
            try {
                $value = $request[$requestParam];
                $gathered[$requestParam] = \Carbon\Carbon::parse($value);
                $action = explode(':', $rule);
                if(count($action) < 1 || !$request->{$action[1]})
                    continue;
                if(!array_key_exists($action[1], $gathered))
                    $gathered[$action[1]] = Carbon::parse($request[$action[1]]);
                $targetDate = $gathered[$action[1]];
                switch($action[0]){
                    case 'lower':
                        if($gathered[$requestParam] > $targetDate)
                            self::throwResponse("{$action[1]} " . translate('is lower than') . " $requestParam", 400);
                    break;
                    case 'equal':
                        if($gathered[$requestParam] != $targetDate)
                            self::throwResponse("{$action[1]} " . translate('is not equal to') . " $requestParam", 400);
                    break;
                    case 'bigger':
                        if($gathered[$requestParam] < $targetDate)
                            self::throwResponse("{$action[1]} " . translate('is bigger than') . " $requestParam", 400);
                    break;
                }
            } catch (\Throwable $th) {
                self::throwResponse($th->getMessage(), 500);   
            }
        }
        return true;
    }

    /**
     * Performes a validation on sent image in binary and checks its format and size (max allowed = 2mb). 
     * @param Binary image
     * @param Bool returnError - default = false
     * @return Bool|\Illuminate\Http\JsonResponse
     */
    public static function validateImage($image = null, $returnError = false)
    {
        if(!$image){
            self::throwResponse(translate("invalid image"), 400);
        }
        $fileHandler = new FileHandler($image);
        if(!$fileHandler->isExtensionValid()){
            self::throwResponse(translate("invalid image format"), 400);
        }
        if(!$fileHandler->isFileSizeValid()){
            self::throwResponse(translate("image too big, max of 2 mb allowed"), 400);
        }
        return $fileHandler;
    }

    /**
     * Performes a validation on sent cnpj 
     * @param String cnpj
     * @return Bool|\Illuminate\Http\JsonResponse
     */
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
            self::throwResponse(translate('cnpj is invalid'), 400);
        return true;
    }

    /**
     * Performes a validation sent array, checking by a query if id exists on table.
     * Obs: may not return an object if sent 'data' key is null or empty, always check if $attrName exists in return Array. To check only if an id was
     * sent use the 'required' attribute!
     * @param Array - Schema: [$attrName => ['object'=> $objectPath , 'data' => $objectId, 'required' => bool,
     *      'attrToCheck' => 'object attribute to check', 'expectedValue' => 'the value expected at $attrToCheck']
     * ] 
     * @return Array [$attrName => $objectInstance]
     */
    public static function checkExistanceOnTable($dataToCheck = [])
    {
        $objects = [];
        foreach($dataToCheck as $key => $data){
            if(!$data['data'])
                continue;
            $required = array_key_exists('required', $data) && is_bool($data['required']) ? $data['required'] : false;
            if(!array_key_exists('object', $data) || !array_key_exists('data', $data))
                self::throwResponse(translate('invalid parameters for validation'), 500);
            try {
                $key = array_key_exists('id', $data) ? $data['id'] : $key;
                $objInstance = new $data['object']();
                if(is_array($data['data'])){
                    $result = $objInstance::whereIn($key, $data['data'])->get();
                    $foundObject = count($result) == count($data['data']) ? $result : null;
                }else{
                    $foundObject = $objInstance->find($data['data']);
                }
            } catch (\Throwable $th) {
                self::throwResponse("$key " . translate('is not valid'), 400);
            }
            if(!$foundObject && $required)
                self::throwResponse(translate('invalid parameters for validation'), 500);
            if(!$required && !$foundObject && $data['data'])
                returnResponse(['message' => translate('invalid parameters for validation'), 'checkParam' => $key], 500);
            $attrToCheck = array_key_exists('attrToCheck', $data) ? $data['attrToCheck'] : false;
            $expectedValue = array_key_exists('expectedValue', $data) ? $data['expectedValue'] : false;
            if($attrToCheck && $expectedValue && $foundObject->{$attrToCheck} != $expectedValue)
                self::throwResponse(translate("invalid type of") . ' ' . $attrToCheck, 400);
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

    /**
     * Validates sent password an return detailed messages about the password
     * @param String password
     * @return Bool
     */
    public static function validatePassword($password = '')
    {
        if(!$password){
            self::throwResponse(ucfirst(translate('a password is required')));
        }
        if(strlen($password) < 6){
            self::throwResponse(ucfirst(translate('password is too shot, it must have more than 6 characters length')));
        }
        if(strlen($password) > 20){
            self::throwResponse(ucfirst(translate('password is too long, it must have more than 20 characters length')));
        }
        $regexChecks = [
            '/[a-z]/'      => ucfirst(translate('invalid password, enter a lowercase letter')),
            '/[A-Z]/'      => ucfirst(translate('invalid password, enter an uppercase letter')),
            '/[0-9]/'      => ucfirst(translate('invalid password, enter a number')),
            '/[@$!%*#?&]/' => ucfirst(translate('invalid password, enter a special character'))
        ];
        foreach($regexChecks as $regex => $errorMessage){
            if(!preg_match($regex, $password))
                self::throwResponse($errorMessage);
        }
        return true;
    }

    /**
     * Terminate application and return a json response with sent message and code
     * @param String message
     * @param Int code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function throwResponse($message = '', $code = 400)
    {
        returnResponse(['message' => $message], $code);
        // response()->json(['message' => $message], $code, [
        //     'Access-Control-Allow-Origin' => env('FRONTEND_URL'), 
        //     'Access-Control-Allow-Credentials' => 'true',
        //     'Content-Type' => 'application/json'
        // ])->send();
        // die();
    }
}
