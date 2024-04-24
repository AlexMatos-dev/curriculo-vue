<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ListLangue;
use Illuminate\Support\Facades\Cache;
use App\Models\Person;
use App\Models\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     * @param Stirng email    - required
     * @param String password - required
     * @param String personType (professional, recruiter or company)
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        Validator::validateParameters($this->request, [
            'email' => 'email|required',
            'password' => 'max:88|required',
            'personType' => 'in:' . Person::PROFESSIONAL_PERSON_ACCOUNT . ',' . Person::RECRUITER_PERSON_ACCOUNT . ',' . Person::COMPANY_PERSON_ACCOUNT
        ]);
        $credentials = request(['email', 'password']);
        $person = Person::where('person_email', $credentials['email']) ->first();
        if(!$person || !Hash::check($credentials['password'], $person->person_password))
            return response()->json(['message' => 'invalid credentials'], 401);
        $token = auth('api')->login($person);
        $key = "lastLoginOf--{$person->person_id}";
        $personType = '';
        if(Cache::has($key)){
            $personType = Cache::get($key);
        }else if(request('personType')){
            $personType = request('personType');
            Cache::put($key, $personType);
        }
        $dataToReturn = $this->respondWithToken($token)->getOriginalContent();
        $dataToReturn['lastLogin'] = $personType;
        return response()->json($dataToReturn);
    }

    /**
     * Creates a new person account.
     * @param String person_username - required
     * @param String person_email - required
     * @param String person_password - required (An uppercase and lowecase character, a number, a special character and more than 6 character length)
     * @param String person_ddi
     * @param String person_phone
     * @param Int person_langue - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function register()
    {
        Validator::validateParameters($this->request, [
            'person_username' => 'required|max:300',
            'person_email' => 'required|max:200|email|unique:persons',
            'person_password' => [
                'min:6',              
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
                'max:80'
            ],
            'person_ddi' => 'max:10',
            'person_phone' => 'max:20',
            'person_langue' => 'required|Integer'
        ]);
        if(!ListLangue::find(request('person_langue')))
            return response()->json(['message' => 'invalid person language'], 400);
        $person = Person::create([
            'person_username' => request('person_username'),
            'person_email' => request('person_email'),
            'person_password' => Hash::make(request('person_password')),
            'person_ddi' => request('person_ddi') ? str_replace(['.', '-'], '', request('person_ddi')) : null,
            'person_phone' => request('person_phone') ? str_replace(['.', '-'], '', request('person_ddi')) : null,
            'person_langue' => request('person_langue')
        ]);
        if(!$person)
            return response()->json(['message' => 'person not created'], 500);
        return response()->json($person);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json($this->respondWithToken(auth('api')->refresh())->getOriginalContent());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
