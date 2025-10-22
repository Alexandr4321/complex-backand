<?php

namespace App\Auth\Controllers\Api;

use App\Auth\Models\User;
use App\Auth\Models\PasswordReset;
use App\Auth\Notifications\PasswordResetRequest;
use App\Auth\Notifications\PasswordResetSuccess;
use App\System\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;


class PasswordResetController extends Controller
{
    protected $validations = [
        'email' => ['required', 'string', 'email', 'max:255'],
        'password' => ['required', 'string', 'min:6'],
    ];
    
 
    public function getFind($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
    
        if (!$passwordReset) {
            return $this->response([], [
                'error' => 'Token is invalid'
            ], 404);
        }
        
        if (Carbon::parse($passwordReset->updated_at)->addHours(12)->isPast()) {
            $passwordReset->delete();
    
            return $this->response([], [
                'error' => 'Token is invalid'
            ], 404);
        }
        
        return $this->response([
            'reset' => $passwordReset,
        ]);
    }
    
    public function postCreate(Request $request)
    {
        $this->validateRequest(['email']);
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return $this->response([], [
                'error' => 'We can`t find a user with that e-mail address.'
            ], 404);
        }
        
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60),
            ]
        );
        
        if ($user && $passwordReset) {
            $user->notify(new PasswordResetRequest($passwordReset->token));
        }
        
        return $this->response([
            'success' => 'Reset link was send to the email'
        ], 201);
    }
    
    public function postReset(Request $request)
    {
        $this->validateRequest();
        
        $passwordReset = PasswordReset::where([
            ['token', request('token')],
            ['email', request('email')],
        ])->first();
        
        if (!$passwordReset) {
            return $this->response([], [
                'error' => 'Token is invalid'
            ], 404);
        }
        
        $user = User::where('email', $passwordReset->email)->first();
        
        if (!$user) {
            return $this->response([], [
                'error' => 'We can`t find a user with that e-mail address'
            ], 404);
        }
        
        $user->password = bcrypt($request->password);
        $user->save();
        
        $passwordReset->delete();
        
        $user->notify(new PasswordResetSuccess());
        
        return response([], [
            'success' => 'Password was changed',
        ]);
    }
}
