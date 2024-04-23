<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\Person;
use App\Models\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
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
            return response()->json(['message' => 'unauthorized'], 401);
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
        return $dataToReturn;
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
        return $this->respondWithToken(auth('api')->refresh());
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
