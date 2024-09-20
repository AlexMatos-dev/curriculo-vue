<?php

namespace App\Helpers;

class Utils
{
    /**
     * Generates a token
     * @param Integer length - default = 30
     * @return String
     */
    public static function generateToken(int $length = 30)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
}

