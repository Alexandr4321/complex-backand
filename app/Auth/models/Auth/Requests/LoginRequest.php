<?php

namespace App\Auth\Requests;

use App\Auth\Helpers\UserService;
use App\Auth\Models\User;
use App\System\Requests\Request;

class LoginRequest extends Request
{
    public function rules()
    {
        return [
            'login' => [
                'required',
            ],
            'password' => [
                'required',
            ],
        ];
    }
}
