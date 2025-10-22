<?php

namespace App\Auth\Requests;

use App\System\Requests\Request;

class EmailTokenRequest extends Request
{

    public function rules()
    {
        return [
            'token' => [ 'required', 'string', ],
        ];
    }
}
