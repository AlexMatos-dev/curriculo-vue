<?php

namespace App\Models;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Validator
{
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
}