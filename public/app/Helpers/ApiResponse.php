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
    $incomingOrigin = array_key_exists('HTTP_ORIGIN', $_SERVER) ? $_SERVER['HTTP_ORIGIN'] : '';
    if(!$incomingOrigin)
        $incomingOrigin = $_SERVER['HTTP_HOST'];
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

/**
 * Saves at TMP txt file. DTF stands for "DEBUG TO FIND"
 * @param String data
 * @param String fileName - default = 'default_log'
 * @return Bool
 */
function dtf($text = '', $name = 'default_log'){
    // Quanto a pesquisa pelas VAGAS, talvez se fizer um JOIN melhor sejá mais rápido, até pq tem que trazer mais joins nela agr
    if($name == '')
        return false;
    $path = storage_path("app/tmp/$name.txt");
    if(!file_exists($path))
        file_put_contents($path, '');
    $val = file_get_contents($path);
    $val = $val ? "$val\n\n$text" : $text;
    $now = \Carbon\Carbon::now();
    $date = $now->format('Y-m-d H:i:s') . '.' . str_pad($now->micro, 6, '0', STR_PAD_RIGHT);
    $val .= "    <-->    $date";
    file_put_contents($path, $val);
    return true;
}