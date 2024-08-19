<?php

namespace App\Http\Middleware;

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
        if(!Session()->has('user_id') || !Session()->get('user_id')){
            Session()->put('web_message', translate('not authenticated'));
            Session()->put('web_message_type', 'error');
            return redirect('/login');
        }
        return $next($request);
    }
}