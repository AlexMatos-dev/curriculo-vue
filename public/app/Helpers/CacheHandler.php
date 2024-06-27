<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Helper for cache manipulation and time until expiration
 */
class CacheHandler{
    private $cacheName;
    private $cache;
    public function __construct(String $cacheName = null){
        $this->cacheName = $cacheName;
        if($cacheName)
            $this->cache = Cache::get($cacheName);
    }

    /**
     * Sets cache with default schema for time until expiration
     * @param String name
     * @param String|Array value
     * @param Int seconds - default = 60
     * @return Nill
     */
    public function setCache($name, $value, $seconds = 60)
    {
        $expirationTime = Carbon::now()->addSeconds($seconds);
        Cache::put($name, ['content' => $value, 'expiration' => $expirationTime], $seconds);
        $this->cacheName = $name;
        $this->cache = $this->getCache($this->cacheName);
    }

    /**
     * Gets cache
     * @param String cacheName - if not sent will get from sent information at the constructor
     * @return CacheData|False
     */
    public function getCache($cacheName = null)
    {
        if($cacheName && Cache::has($cacheName)){
            $this->cache = Cache::get($cacheName);
        }else{
            return false;
        }
        return $this->cache ? $this->cache : false;
    }

    /**
     * Gets the cache content
     * @return String|Array
     */
    public function getCacheContent()
    {
        if(!$this->cache)
            return false;
        if(is_array($this->cache) && array_key_exists('content', $this->cache))
            return $this->cache['content'];
        return $this->cache;
    }

    /**
     * Gets the cache expiration time. If it was created by this helper
     * @return Int
     */
    public function getExpirationTime()
    {
        if(!array_key_exists('expiration', $this->cache))
            return 0;
        $now = Carbon::now();
        $secondsLeft = $this->cache['expiration']->diffInSeconds($now, false);
        return number_format(abs($secondsLeft), 0);
    }

    /**
     * Checks if cache exists
     * @return Bool
     */
    public function cacheExist()
    {
        return $this->cache ? true : false;
    }

    /**
     * Removes cache
     * @return Bool
     */
    public function removeCache()
    {
        return $this->cache ? Cache::forget($this->cacheName) : false;
    }
}