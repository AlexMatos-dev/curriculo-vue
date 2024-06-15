<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ListLangue;
use Illuminate\Support\Facades\Cache;
use App\Models\Person;
use App\Helpers\Validator;
use App\Models\ListCountry;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login route, which return a token.
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
            return response()->json(['message' => translate('invalid credentials')], 401);
        if(!$person->email_verified_at)
            return response()->json(['message' => translate('email not verified')], 406);
        $key = "lastLoginOf--{$person->person_id}";
        $personType = '';
        if(Cache::has($key)){
            $personType = Cache::get($key);
        }else if(request('personType')){
            $personType = request('personType');
            Cache::put($key, $personType);
        }
        $token = $person->createToken('auth_token')->plainTextToken;
        $person->last_login = Carbon::now();
        $person->save();
        Auth::login($person);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'personType' => $personType,
            'profiles' => (new Profile())->getProfileStatus($person->person_id),
            'person' => $person
        ]);
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
            'person_password' => Validator::getPersonPasswordRule(),
            'person_ddi' => 'max:10',
            'person_phone' => 'max:20',
            'person_langue' => 'integer'
        ]);
        if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[\w$@]{8,}$/', request('person_password')))
            return response()->json(translate('the password must have a letter, a lowercase number and be between 8 and 20 characters long'), 400);
        if(request('person_langue') && !ListLangue::find(request('person_langue')))
            return response()->json(['message' => translate('invalid person language')], 400);
        if(request('person_phone') && !request('person_ddi'))
            return response()->json(['message' => translate('ddi is required')], 400);
        if(!request('person_phone') && request('person_ddi'))
            return response()->json(['message' => translate('phone number is required')], 400);
        if(request('ddi') && !ListCountry::where('ddi', request('ddi'))->first())
            return response()->json(['message' => translate('invalid ddi')], 400);
        $person_phone = request('person_phone');
        if(request('person_phone'))
            $person_phone = preg_replace('/[^0-9]/', '', $person_phone);
        $person = Person::create([
            'person_username' => request('person_username'),
            'person_email' => request('person_email'),
            'person_password' => Hash::make(request('person_password')),
            'person_ddi' => request('person_ddi'),
            'person_phone' => $person_phone,
            'person_langue' => request('person_langue')
        ]);
        if(!$person)
            return response()->json(['message' => translate('person not created')], 500);
        return response()->json($person);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        $personObj = Auth::user();
        $professional = $personObj->getProfile(Profile::PROFESSIONAL);
        if($professional)
            $professional = $professional->gatherInformation();
        $profilesData = (new Profile())->getProfilesByPersonId($personObj->person_id);
        $profilesData['person'] = $personObj;
        return response()->json($profilesData);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if(Auth::user()){
            Auth::logout();
        }else{
            auth('api')->logout();
        }
        Session::flush();
        return response()->json(['message' => translate('successfully logged out')]);
    }

    /**
     * Request a change password code which will be sent to informed Person email
     * @param String email - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestChangePasswordCode()
    {
        Validator::validateParameters($this->request, [
            'email' => 'email|required'
        ]);
        $person = Person::where('person_email', request('email'))->first();
        if(!$person)
            return response()->json(['message' => translate('invalid email')], 400);
        if(Cache::has("awaiting_changepass-email-receival-{$person->person_id}"))
            return response()->json(['message' => translate('code already sent, wait 1 minute')], 500);
        if(!$person->sendRequestChangePasswordCodeEmail())
            return response()->json(['message' => translate('email not sent')], 500);
        Cache::put("awaiting_changepass-email-receival-{$person->person_id}", 'email sent', 60);
        return response()->json(['message' => translate('email sent')], 200);
    }

    /**
     * Changes logged Person password. 
     * Obs: Code will only be usable once
     * @param String code - required
     * @param String newPassword - required (An uppercase and lowecase character, a number, a special character and more than 6 character length)
     * @param String email - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword()
    {
        Validator::validateParameters($this->request, [
            'code' => 'required',
            'newPassword' => Validator::getPersonPasswordRule(),
            'email' => 'email|required'
        ]);
        $person = Person::where('person_email', request('email'))->first();
        if(!$person)
            return response()->json(['message' => 'invalid email'], 400);
        if(Hash::check(request('newPassword'), $person->person_password))
            return response()->json(['message' => translate('invalid password')], 400);
        $cache = Cache::get('resetPasswordCode--'.$person->person_id);
        if($cache != request('code'))
            return response()->json(['message' => translate('invalid code')], 400);
        $person->person_password = Hash::make(request('newPassword'));
        if(!$person->save())
            return response()->json(['message' => translate('password not saved')], 500);
        Cache::forget("resetPasswordCode--{$person->person_id}");
        Cache::forget("awaiting_changepass-email-receival-{$person->person_id}");
        return response()->json(['message' => translate('password updated')], 200);
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
            'expires_in' => Auth::user()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Request an email verification code which will be sent to informed Person email
     * @param String email - required
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestEmailConfirmationCode()
    {
        Validator::validateParameters($this->request, [
            'email' => 'email|required'
        ]);
        $person = Person::where('person_email', request('email'))->first();
        if(!$person)
            return response()->json(['message' => translate('invalid email')], 400);
        if($person->email_verified_at)
            return response()->json(['message' => translate('email already verified')], 200);
        if(Cache::has("awaiting-emailverification-email-receival-{$person->person_id}"))
            return response()->json(['message' => translate('code already sent, wait 1 minute')], 500);
        if(!$person->sendEmailVerificationCodeEmail())
            return response()->json(['message' => translate('email not sent')], 500);
        $person->email_verified_at = null;
        $person->save();
        Cache::put("awaiting-emailverification-email-receival-{$person->person_id}", 'email sent', 60);
        return response()->json(['message' => translate('email sent')], 200);
    }

    /**
     * Verifies sent email 
     * Note: Code will only be usable once
     * @param String code - required
     * @param String email - required
     * @param String newEmail - required (must not be in use at persons)
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail()
    {
        Validator::validateParameters($this->request, [
            'code' => 'required',
            'email' => 'required'
        ]);
        $person = Person::where('person_email', request('email'))->first();
        if(!$person)
            return response()->json(['message' => 'invalid email'], 400);
        $cache = Cache::get('verifyEmailCode--'.$person->person_id);
        if($cache != request('code'))
            return response()->json(['message' => translate('invalid code')], 400);
        $person->email_verified_at = Carbon::now();
        if(!$person->save())
            return response()->json(['message' => translate('email not verified')], 500);
        Cache::forget("verifyEmailCode--{$person->person_id}");
        Cache::forget("awaiting-emailverification-email-receival-{$person->person_id}");
        return response()->json(['message' => translate('email verified')], 200);
    }
}
