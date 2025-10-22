<?php

namespace App\Auth\Requests;

use App\System\Requests\Request;

class PhoneTokenRequest extends Request
{
    public function rules()
    {
        return [
            'token' => [ 'required', 'integer', ],
        ];
    }
}
