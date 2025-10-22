<?php

namespace App\Auth\Requests;

use App\Auth\Models\User;
use App\System\Requests\Request;

class RegisterRequest extends Request
{
    public function rules()
    {
        return [
            'iin' => [
                'nullable',
                'string'
            ],
            'password' => [
                'required',
                'string',
            ],
            'email' => [
                'nullable',
                'email',
            ],
            'fullName' => [
                'nullable',
                'string',
            ],
            'phone' => [
                'required',
                'string',
            ],
            'type' => [
                'required',
                'string',
                'in:customer,deliverer'
            ],
        ];
    }
}
