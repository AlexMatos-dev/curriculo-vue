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