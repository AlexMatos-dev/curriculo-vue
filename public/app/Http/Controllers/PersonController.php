<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonController extends Controller
{
    /**
     * Updates logged person account.
     * @param String person_username - required
     * @param String person_email - required
     * @param String person_ddi
     * @param String person_phone
     * @param Int person_langue - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        Validator::validateParameters($this->request, [
            'person_username' => 'required|max:300',
            'person_email' => 'required|max:200|email',
            'person_ddi' => 'max:10',
            'person_phone' => 'max:20',
            'person_langue' => 'required|Integer'
        ]);
        $person = Auth::user();
        if(request('person_email') != $person->person_email && Person::where('person_email', request('person_email')->first()))
            return response()->json(['message' => 'invalid email'], 400);
        $result = $person->update([
            'person_username' => request('person_username'),
            'person_email' => request('person_email'),
            'person_ddi' => request('person_ddi') ? str_replace(['.', '-'], '', request('person_ddi')) : null,
            'person_phone' => request('person_phone') ? str_replace(['.', '-'], '', request('person_ddi')) : null,
            'person_langue' => request('person_langue')
        ]);
        if(!$result)
            return response()->json(['message' => translate('person not updated')], 500);
        return response()->json($person);
    }
}
