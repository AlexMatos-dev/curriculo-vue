<?php

namespace App\Http\Middleware;

use App\Models\ListLangue;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->fetchUserLanguage($request);
        return $next($request);
    }

    /**
     * Sets this user language by sent user_lang value or its Ip location
     */
    public function fetchUserLanguage(Request $request)
    {
        if($request->has('user_lang')){
            $lang = $request->user_lang;
        }else if(Session()->has('user_lang')){
            $lang = Session()->get('user_lang');
        }else{
            $IpGeolocation = new \App\Helpers\IpGeolocation($request->ip());
            try {
                $lang = $IpGeolocation->getMainLanguage();
            } catch (\Throwable $th) {
                $lang = ListLangue::DEFAULT_LANGUAGE;
            }
        }
        $this->changeSessionLanguage($lang);
    }

    /**
     * Checks send languageISO to check its validaty, if is not validy set the default language
     * @param languageISO
     */
    public function changeSessionLanguage($languageISO = ListLangue::DEFAULT_LANGUAGE)
    {
        if(!ListLangue::where('llangue_acronyn', $languageISO)->first())
            $languageISO = ListLangue::DEFAULT_LANGUAGE;
        Session()->put('user_lang', $languageISO ? $languageISO : ListLangue::DEFAULT_LANGUAGE);
    }
}
