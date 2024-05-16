<?php

namespace App\Http\Middleware;

use App\Helpers\Validator;
use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use App\Models\Professional;
use App\Models\Profile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MyCurriculum extends Controller
{
    private $curriculumId = 'curriculum_id';
    /**
     * This middleware filters request, the logged person may only have access to its own data (its professional profile curriculums and subsequents Objects)
     * The "handle" methods checks:
     * Fetches Professional object of logged person account & sets it to session
     * Fetches Curriculum object of logged person account & sets it to session if 'curriculum_id' is sent and route is not an exception (not to check route)
     * Note: This method has an array of exceptions which disable the required parameters such as 'curriculum_id' fetch and check!
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $person = auth('api')->user();
        $professional = $person->getProfile(Profile::PROFESSIONAL);
        if(!$professional)
            Validator::throwResponse('professional not found', 401);
        Session()->put('professional', $professional);
        if(($this->request->route()->getPrefix() != 'api/curriculum' || ($this->isException()) && !request($this->curriculumId)))
            return $next($request);
        $curriculum = Curriculum::where('curriculum_id', request($this->curriculumId))->where('cprofes_id', $professional->professional_id)->first();
        if(!$curriculum)
            Validator::throwResponse('curriculum not found', 400);
        if(!$this->checkCurriculumType($curriculum))
            Validator::throwResponse('curriculum type is invalid for this request', 400);
        Session()->put('curriculum', $curriculum);
        return $next($request);
    }

    /**
     * Validates wheter current request is an exception
     * @return Bool
     */
    public function isException()
    {
        $routeUri = $this->request->route()->uri();
        $routeMethod = $this->request->method();
        if(!array_key_exists($routeUri, $this->getExceptionRoutes()))
            return false;
        return $this->exceptionRoutesContainsMethod($routeUri, $routeMethod);
    }

    /**
     * Checks if key sent exists in exception routes array list and with method is in list key 
     * @param String key
     * @param String method
     * @return Bool
     */
    public function exceptionRoutesContainsMethod($key, $method = '')
    {
        $route = $this->getExceptionRoutes($key);
        if(is_array($route) && array_key_exists('curriculum_id', $route))
            $this->curriculumId = $route['curriculum_id'];
        return !$route || !in_array($method, $route['methods']) ? false : true;
    }

    /**
     * Checks if curriculum is of the correct type for usage
     * @param Curriculum curriculum
     * @return Bool
     */
    public function checkCurriculumType(Curriculum|Null $curriculum)
    {
        if(!$curriculum)
            return false;
        if(in_array($this->request->route()->uri(), ['api/curriculum/curriculum', 'api/curriculum/curriculum/{curriculum}']) || $this->request->method() == 'GET')
            return true;
        return $curriculum->curriculum_type == Curriculum::TYPE_FILE ? false : true;
    } 

    /**
     * Returns the exception route array list or the expected key by parameter
     * @param String key
     * @return Array (List of routes or Route methods)
     */
    public function getExceptionRoutes($key = null)
    {
        $routesList = [
            'api/curriculum/link' => ['methods' => ['GET', 'POST']],
            'api/curriculum/link/{link}' => ['methods' => ['GET', 'DELETE']],
            
            'api/curriculum/visa' => ['methods' => ['POST'], 'curriculum_id' => 'vicurriculum_id'],
            'api/curriculum/visa/{visa}' => ['methods' => ['GET', 'DELETE', 'PUT'], 'curriculum_id' => 'vicurriculum_id'],

            'api/curriculum/curriculum' => ['methods' => ['GET', 'POST']],
            'api/curriculum/curriculum/{curriculum}' => ['methods' => ['GET', 'DELETE']],

            'api/curriculum/education' => ['methods' => ['GET', 'POST'], 'curriculum_id' => 'edcurriculum_id'],
            'api/curriculum/education/{education}' => ['methods' => ['GET', 'DELETE', 'PUT'], 'curriculum_id' => 'edcurriculum_id'],

            'api/curriculum/experience' => ['methods' => ['GET', 'POST'], 'curriculum_id' => 'excurriculum_id'],
            'api/curriculum/experience/{experience}' => ['methods' => ['GET', 'DELETE', 'PUT'], 'curriculum_id' => 'excurriculum_id'],
            
            'api/curriculum/certification' => ['methods' => ['GET', 'POST'], 'curriculum_id' => 'cercurriculum_id'],
            'api/curriculum/certification/{certification}' => ['methods' => ['GET', 'DELETE', 'PUT'], 'curriculum_id' => 'cercurriculum_id'],

            'api/curriculum/reference' => ['methods' => ['GET', 'POST'], 'curriculum_id' => 'refcurriculum_id'],
            'api/curriculum/reference/{reference}' => ['methods' => ['GET', 'DELETE', 'PUT'], 'curriculum_id' => 'refcurriculum_id'],

            'api/curriculum/presentation' => ['methods' => ['GET', 'POST'], 'curriculum_id' => 'precurriculum_id'],
            'api/curriculum/presentation/{presentation}' => ['methods' => ['GET', 'DELETE', 'PUT'], 'curriculum_id' => 'precurriculum_id'],
            
            'api/curriculum/skill' => ['methods' => ['GET', 'POST'], 'curriculum_id' => 'skcurriculum_id'],
            'api/curriculum/skill/{skill}' => ['methods' => ['GET', 'DELETE', 'PUT'], 'curriculum_id' => 'skcurriculum_id']
        ];
        if($key)
            return array_key_exists($key, $routesList) ? $routesList[$key] : null;
        return $routesList;
    }
}
