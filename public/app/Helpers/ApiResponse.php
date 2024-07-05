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
    $incomingOrigin = $_SERVER['HTTP_ORIGIN'];
    $allowedOrigins = explode(',' , env('ALLOWED_ORIGINS'));
    if(!in_array($incomingOrigin, $allowedOrigins)){
        response()->json(['message' => 'unknow'], 500)->header('Access-Control-Allow-Origin', '')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->header('Access-Control-Allow-Credentials', 'true')->send();
    }
    response()->json($data, $code)->header('Access-Control-Allow-Origin', $incomingOrigin)
    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
    ->header('Access-Control-Allow-Credentials', 'true')->send();
    die();
}
