<?php

namespace App\Auth\Requests;

use App\Auth\Models\User;
use App\System\Requests\Request;

class CreateUserRequest extends Request
{
    public function rules()
    {
        return [
            'phone' => [
                'string',
                'max:'.User::getMax('phone'),
                $this->ruleUnique(User::class),
            ],
            'fullName' => [
                'required',
                'string',
                'max:'.User::getMax('fullName'),
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:'.User::getMax('password'),
            ],
        ];
    }
}
