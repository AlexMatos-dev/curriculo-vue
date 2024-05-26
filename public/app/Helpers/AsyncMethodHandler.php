<?php

namespace App\Helpers;

class AsyncMethodHandler{
    const MAIN_METHOD_URL = 'async/asyncactions' ;
    const NOTIFY_JOB_APPLIED_STATUS_CHANGE = 1;

    /**
     * Sends an async request of emails by parameters
     * @param Int notificationType - required
     * @param Array data - Any information to send
     * @return Nill
     */
    public static function sendEmailNotification($notificationType, $data = [])
    {
        if(!$notificationType || !in_array($notificationType, self::getNotificationTypes()) || !auth('api')->user())
            return false;
        $personId = auth('api')->user()->person_id;
        $method = null;
        switch($notificationType){
            case self::NOTIFY_JOB_APPLIED_STATUS_CHANGE:
                $method = 'jobApplied_@_sendStatusEmail';
            break;
        }
        if($method){
            $a = self::httpPost([
                'url' => url(self::MAIN_METHOD_URL),
                'arrayValues' => [
                    'personId' => $personId,
                    'data' => $data,
                    'method' => $method,
                    'asyncRequest' => false
                ]
            ]);
            dd($a);
        }
    }

    /**
     * method responsible for making an http/https request
     * @param array $parameters <indexed array> with   <string> 'url'          - required
     *                                                 <string> 'arrayValues'
     *                                                 <bool>   'asyncRequest' - default is false
     *                                                 <bool>   'getRequest'   - default is false
     *                                                 <int>    'await'        - the seconds to sleep before requesting
     * @return String|False
     */
    public static function httpPost($parameters)
    {
        $url          = (isset($parameters['url'])                     ? $parameters['url']          : null);
        $arrayValues  = (isset($parameters['arrayValues'])             ? $parameters['arrayValues']  : []);
        $asyncRequest = (array_key_exists('asyncRequest', $parameters) ? $parameters['asyncRequest'] : false);
        $getRequest   = (array_key_exists('getRequest', $parameters)   ? true                        : false);
        $await        = (array_key_exists('await', $parameters)        ? true                        : false);
        if(is_null($url)){
            return false;
        }
        if($await){
            sleep((int)($await));
        }
        $headers = [
            'Content-Type: application/json'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if(!$getRequest){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayValues));
        }
        if($asyncRequest){
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        }
        if(in_array($_SERVER['HTTP_HOST'], ['localhost:8080', 'localhost'])){
            // Proxy for Docker
            curl_setopt($ch, CURLOPT_PROXY, $_SERVER['SERVER_ADDR'] . ':' .  $_SERVER['SERVER_PORT']);
        }
        $result = curl_exec($ch);
        $errorMsg = null;
        if(curl_errno($ch)) {
            $errorMsg = curl_error($ch);
            self::logMessage('ERROR at request: ' + $errorMsg);
            return false;
        }
        curl_close($ch);
        return $result;
    }

    /**
     * Gets an array of avaliable notifications types
     * @return Array
     */
    public static function getNotificationTypes()
    {
        return [
            self::NOTIFY_JOB_APPLIED_STATUS_CHANGE
        ];
    }

    /**
     * Logs messages at asyncMethods.log with logging datetime
     * @param String message
     * @return Nill
     */
    public static function logMessage($message = '')
    {
        if(!$message)
            return;
        $path = storage_path('logs/asyncMethods.log');
        if(!file_exists($path))
            file_put_contents($path, '');
        $content = file_get_contents($path);
        $lineBreak = PHP_EOL;
        $now = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        file_put_contents($path, "$content$lineBreak$lineBreak$message -> at: $now");
    }
}