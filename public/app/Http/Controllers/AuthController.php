<?php

namespace App\Http\Controllers;

use App\Helpers\CacheHandler;
use App\Http\Controllers\Controller;
use App\Models\ListLangue;
use Illuminate\Support\Facades\Cache;
use App\Models\Person;
use App\Helpers\Validator;
use App\Models\ListCountry;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     * @param Stirng email    - required
     * @param String password - required
     * @param String personType (professional, recruiter or company)
     * @param Bool remember
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        Validator::validateParameters($this->request, [
            'email' => 'email|required',
            'password' => 'max:88|required',
            'personType' => 'in:' . Person::PROFESSIONAL_PERSON_ACCOUNT . ',' . Person::RECRUITER_PERSON_ACCOUNT . ',' . Person::COMPANY_PERSON_ACCOUNT,
            'remember' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        $person = Person::where('person_email', $credentials['email']) ->first();
        if(!$person || !Hash::check($credentials['password'], $person->person_password))
            returnResponse(['message' => translate('invalid credentials')], 401);
        // if(!$person->email_verified_at)
        //     returnResponse(['message' => translate('email not verified')], 406);
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
            'profiles' => (new Profile())->getProfilesByPersonId($person->person_id),
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
            'person_ddi' => 'max:10',
            'person_phone' => 'max:20',
            'person_langue' => 'integer'
        ]);
        Validator::validatePassword(request('person_password'));
        if(request('person_langue') && !ListLangue::find(request('person_langue')))
            returnResponse(['message' => translate('invalid person language')], 400);
        if(request('person_phone') && !request('person_ddi'))
            returnResponse(['message' => translate('ddi is required')], 400);
        if(!request('person_phone') && request('person_ddi'))
            returnResponse(['message' => translate('phone number is required')], 400);
        if(request('ddi') && !ListCountry::where('ddi', request('ddi'))->first())
            returnResponse(['message' => translate('invalid ddi')], 400);
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
            returnResponse(['message' => translate('person not created')], 500);
        returnResponse($person);
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
        returnResponse($profilesData);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'successfully logged out'], 200)
            ->withCookie(cookie()->forget('XSRF-TOKEN'))
            ->withCookie(cookie()->forget(env('APP_NAME') . '_session'));
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
        returnResponse([
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
            returnResponse(['message' => translate('invalid email')], 400);
        if($person->email_verified_at)
            returnResponse(['message' => translate('email already verified')], 200);
        $cacheHandler = new CacheHandler("awaiting-emailverification-email-receival-{$person->person_id}");
        if($cacheHandler->cacheExist()){
            returnResponse(['message' => translate('code already sent, wait for') . ' ' . $cacheHandler->getExpirationTime() . ' ' . translate('seconds')], 500);
        }
        if(!$person->sendEmailVerificationCodeEmail())
            returnResponse(['message' => translate('email not sent')], 500);
        $person->email_verified_at = null;
        $person->save();
        $cacheHandler->setCache("awaiting-emailverification-email-receival-{$person->person_id}", ['sent' => true], 60);
        returnResponse(['message' => translate('email sent')]);
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
            returnResponse(['message' => translate('invalid email')], 400);
        $cacheHandler = new CacheHandler('verifyEmailCode--'.$person->person_id);
        if(!$cacheHandler->cacheExist() || $cacheHandler->getCacheContent() != request('code'))
            returnResponse(['message' => translate('invalid code')], 400);
        $person->email_verified_at = Carbon::now();
        if(!$person->save())
            returnResponse(['message' => translate('email not verified')], 500);
        $cacheHandler->removeCache();
        Cache::forget("awaiting-emailverification-email-receival-{$person->person_id}");
        returnResponse(['message' => translate('email verified')]);
    }
}
