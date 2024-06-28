<?php

/**
 * Terminate application and return a json response with sent message and code
 * @param String data
 * @param Int code - default = 200
 * @param Array headers
 * @param Array options 
 * @return \Illuminate\Http\JsonResponse
 */
function returnResponse($data = '', $code = 200, $headers = [], $options = 0)
{
    $defaultHeaders = [
        'Access-Control-Allow-Origin' => env('FRONTEND_URL'), 
        'Access-Control-Allow-Credentials' => 'true',
        'Content-Type' => 'application/json',
        'Access-Control-Allow-Headers' => ['Content-Type, Authorization'],
        'Access-Control-Allow-Methods' => ['GET, POST, PUT, DELETE, OPTIONS'] 
    ];
    if(!empty($headers))
        $defaultHeaders = array_merge($defaultHeaders, $headers);
    response()->json($data, $code)->header('Access-Control-Allow-Origin', 'http://localhost:8080')
    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
    ->header('Access-Control-Allow-Credentials', 'true')->send();
    // response()->json($data, $code, $headers, $options)->send();
    die();
}