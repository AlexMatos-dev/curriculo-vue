<?php

namespace App\Http\Controllers\Web;

use App\Helpers\Validator;
use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('login', []);
    }

    public function authenticate()
    {
        $requestedView = request('view');
        $email = request('email');
        $password = request('password');
        $user = User::where('email', $email)->first();
        if(!$user)
            returnResponse(['success' => false, 'message' => translate('user not found')]);
        if(!Hash::check($password, $user->password))
            returnResponse(['success' => false, 'message' => translate('user not found')]);
        if(!$user->loginAdminUser())
            returnResponse(['success' => false, 'message' => translate('user not logged')]);
        $user->users = $user->createToken('auth_token')->plainTextToken;
        if(!$user->save())
            returnResponse(['success' => false, 'message' => translate('a problem ocorred, refresh the page and try again')]);
        returnResponse(['success' => true, 'message' => translate('user logged'), 'view' => view($requestedView)->render(), 'token' => $user->users]);
    }

    public function saveAdmin()
    {
        $id       = request('id');
        $email    = request('email');
        $name     = request('name');
        $password = request('password');
        if(User::where('email', $email)->first())
            return response()->json(['success' => false, 'message' => translate('email in use')]);
        $user = new User();
        if($id){
            $user = User::find($id);
            if(!$user)
                return response()->json(['success' => false, 'message' => translate('user not found')]);
        }
        $user->email = $email;
        $user->name = $name;
        $user->password = Hash::make($password);
        $user->role = User::ADMIN_ROLE;
        if(!$user->save())
            return response()->json(['success' => false, 'message' => translate('not saved')]);
        return response()->json(['success' => true, 'message' => translate('user saved')]); 
    }

    /**
     * Resets session message
     */
    public function cleanSessionMessage()
    {
        Session()->forget('web_message');
        Session()->forget('web_message_type');
    }
}
