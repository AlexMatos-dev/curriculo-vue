<?php

namespace App\Http\Controllers;

use App\Helpers\AsyncMethodHandler;
use App\Helpers\Validator;
use App\Models\JobApplied;
use App\Models\Person;

class AsyncActionController extends Controller
{
    /**
     * Handles incoming request of async methods
     * @param Int personId - required
     * @param String method - schema: '$classFlag_@_$methodToCall'
     * @return JsonResponse {"message":String}
     */
    public function handler()
    {
        AsyncMethodHandler::logMessage("the person of id {$this->request->personId} requested an async action using the following parameters: {$this->request->method}");
        if(!$this->request->method){
            Validator::throwResponse('no method sent', 400);
        }
        $person = Person::find($this->request->personId);
        if(!$person){
            Validator::throwResponse('no person id sent', 400);
        }
        $avaliableClasses = [
            'jobApplied' => JobApplied::class 
        ];
        try {
            $information = explode('_@_', $this->request->method);
            Session()->put('asyncLoggedPerson', $person);
            $result = (new ($avaliableClasses[$information[0]])())->{$information[1]}($this->request->data);
            if(!$result)
                Validator::throwResponse('an error occured', 500);
            Validator::throwResponse('executed with success', 200);
        } catch (\Throwable $th) {
            Validator::throwResponse('internal error: '.$th->getMessage(), 500);
        }
    }
}
