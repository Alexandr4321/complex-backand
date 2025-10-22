<?php

namespace App\Auth\Requests;

use App\Auth\Models\User;
use App\System\Requests\Request;

class ResetPasswordRequest extends Request
{
    public function rules()
    {
        return [
            'phone' => [
                'required',
                'string'
            ],
            'password' => [
                'required',
                'string'
            ],
        ];
    }
}
