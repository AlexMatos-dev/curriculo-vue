<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\CommonCurrency;
use App\Models\ListLangue;
use App\Models\Person;
use Illuminate\Support\Facades\Auth;

class PersonController extends Controller
{
    /**
     * Updates logged person account.
     * @param String person_username - required
     * @param String person_email - required
     * @param String person_ddi
     * @param String person_phone
     * @param Int person_langue
     * @param Int currency
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        Validator::validateParameters($this->request, [
            'person_username' => 'required|max:300',
            'person_email' => 'required|max:200|email',
            'person_ddi' => 'max:10',
            'person_phone' => 'max:20',
            'person_langue' => 'integer',
            'currency' => 'integer'
        ]);
        $person = Auth::user();
        if(request('person_email') != $person->person_email && Person::where('person_email', request('person_email')->first()))
            returnResponse(['message' => 'invalid email'], 400);
        Validator::checkExistanceOnTable([
            'person_langue' => ['object' => ListLangue::class,     'data' => $this->request->person_langue, 'required' => false],
            'currency'      => ['object' => CommonCurrency::class, 'data' => $this->request->currency,      'required' => false]
        ]);
        $result = $person->update([
            'person_username' => request('person_username'),
            'person_email' => request('person_email'),
            'person_ddi' => request('person_ddi'),
            'person_phone' => request('person_phone'),
            'person_langue' => request('person_langue'),
            'currency' => request('currency')
        ]);
        if(!$result)
            returnResponse(['message' => translate('person not updated')], 500);
        returnResponse($person);
    }
}
