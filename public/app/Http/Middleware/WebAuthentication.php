<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebAuthentication
{
    /**
     * Checks if admin user is logged in.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->token || !User::where('users', $request->token)->first()){
            Session()->put('web_message', translate('not authenticated'));
            Session()->put('web_message_type', 'error');
            $view = $this->getViewByPath($request->getRequestUri());
            if(!$view)
                exit(view('notFound'));
            exit(view('login')->with(['view' => $view]));
        }
        return $next($request);
    }

    public function getViewByPath($uri)
    {
        $views = [
            '/translations' => 'systemTranslation',
            '/swagger' => 'swagger'
        ];
        if(!array_key_exists($uri, $views))
            return false;
        return $views[$uri];
    }
}