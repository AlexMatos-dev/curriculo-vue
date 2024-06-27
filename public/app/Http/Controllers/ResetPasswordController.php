<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Helpers\Validator;
use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /**
     * Requests a password reset email
     * @param String email
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail()
    {
        Validator::validateParameters($this->request, [
            'email' => 'required|email'
        ]);
        $person = Person::where('person_email', request('email'))->first();
        if(!$person)
            return response()->json(['message' => translate('invalid email')], 400);
        $response = Password::broker('persons')->sendResetLink(
            ['person_email' => $person->person_email]
        );
        if($response == Password::RESET_THROTTLED)
            return response()->json(['message' => translate('wait before resending password reset email')], 500);
        if($response != Password::RESET_LINK_SENT)
            return response()->json(['message' => translate('email not sent')], 500);
        return response()->json(['message' => translate('email sent')], 200);
    }

    /**
     * Resets the person password
     * @param String token
     * @param String email
     * @param String password
     * @param String password_confirmation
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset()
    {
        Validator::validateParameters($this->request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => Validator::getPersonPasswordRule(),
            'password_confirmation' => Validator::getPersonPasswordRule(),
        ]);
        if(request('password') != request('password_confirmation'))
            return response()->json(['message' => translate('passwords do not match')], 400);
        $person = Person::where('person_email', request('email'))->first();
        if(!$person)
            return response()->json(['message' => translate('invalid email')], 400);
        $passswordResetToken = PasswordResetToken::find(request('email'));
        if(!$passswordResetToken)
            return response()->json(['message' => translate('no reset password found, request a new reset password email')], 400);
        if(!$passswordResetToken->isTokenValid(request('token')))
            return response()->json(['message' => translate('token is invalid')], 400);
        $person->person_password = Hash::make(request('password'));
        if(!$person->save())
            return response()->json(['message' => translate('password not reset with success')], 500);
        $passswordResetToken->delete();
        return response()->json(['message' => translate('password reset with success')]);
    }
}
