<?php

namespace App\Http\Middleware;

use App\Helpers\Validator;
use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiGuard extends Controller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->sanitazeParameters();
        return $next($request);
    }

    /**
     * Verifies received parameters and sanitaze them all
     */
    public function sanitazeParameters()
    {
        $input = $this->request->all();
        array_walk_recursive($input, function(&$value) {
            $value = strip_tags(trim($value));
        });
        $suspectPatterns = [
            '/\bSELECT\b/i',
            '/\bUNION\b/i',
            '/\bINSERT\b/i',
            '/\bUPDATE\b/i',
            '/\bDELETE\b/i',
            '/\bDROP\b/i',
            '/\b--\b/i'
        ];
        foreach ($input as $key => $value) {
            foreach ($suspectPatterns as $pattern) {
                if(!is_array($value))
                    $value = [$value];
                foreach($value as $value){
                    if (preg_match($pattern, $value))
                        Validator::throwResponse('detected SQL injection attempt');
                }
            }
        }
        $this->request->merge($input);
    }
}
