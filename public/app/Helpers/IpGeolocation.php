<?php

namespace App\Helpers;

/**
 * Class created at 10/06/2024 to handle API response and call, making use of the api from: https://api.ipgeolocation.io and with a free API key
 * @author Nickolas Alvaro Bini
 */
class IpGeolocation{
    protected $apiKey;
    private $requestIp;
    private $lang;
    public function __construct($ip = null, $lang = 'en', $customApiKey = null) {
        $this->requestIp = $ip ? $ip : $_SERVER['REMOTE_ADDR'];
        $this->apiKey = $customApiKey;
        $this->lang = $lang;
    }

    /**
     * Parses result from ipgeolocation API and return the result
     * @param Array
     */
    public function get()
    {
        $response = json_decode($this->consumeAPI(), true);
        if(!$response)
            return false;
        if(!array_key_exists('languages', $response))
            return ['en'];
        $spokenLanguagues = [];
        foreach(explode(',', $response['languages']) as $language){
            $isoCode = explode('-', $language)[0];
            $isoCode = explode('_', $isoCode)[0];
            if(!in_array($isoCode, $spokenLanguagues))
                $spokenLanguagues[] = $isoCode;
        }
        $response['simplifiedLanguages'] = $spokenLanguagues;
        return $response;
    }

    /**
     * Returns the first result from ipgeolocation API
     * @return String|False
     */
    public function getMainLanguage()
    {
        $response = $this->get();
        if(!$response || empty($response))
            return false;
        return $response['simplifiedLanguages'][0];
    }

    /**
     * Consumes the api.ipgeolocation.io service which returns information accordingly from IP
     * @param String fields - default: '*'
     * @param Stirng excludes - default = ''
     * @return String
     */
    public function consumeAPI($fields = '*', $excludes = '')
    {
        $apiKey = !$this->apiKey ? env('IPGEOLOCATION_TOKEN') : $this->apiKey;
        $url = "https://api.ipgeolocation.io/ipgeo?apiKey=".$apiKey."&ip=".$this->requestIp."&lang=".$this->lang."&fields=".$fields."&excludes=".$excludes;
        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_HTTPGET, true);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: '.$_SERVER['HTTP_USER_AGENT']
        ));
        return curl_exec($cURL);
    }
}