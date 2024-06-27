<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class PasswordResetToken extends Model
{
    protected $primaryKey = 'email';
    protected $table = 'password_reset_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'token',
        'created_at'
    ];

    /**
     * Checks if token is invalid by comparing it and checking if determined time passed
     * @param String token
     * @param Int validTime - default: 30
     * @return Bool
     */
    public function isTokenValid($token = '', $validTime = 30)
    {
        if(!Hash::check($token, $this->token))
            return false;
        $now = Carbon::now();
        $differenceInMinutes = abs(floor(($now->diffInSeconds($this->created_at)) / 60));
        if($differenceInMinutes > $validTime){
            $this->delete();
            return false;
        }
        return true;
    }
}