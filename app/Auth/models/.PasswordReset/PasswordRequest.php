<?php

namespace App\Auth\Requests;

use App\System\Requests\Request;


class PasswordRequest extends Request
{
    
    /**
     * @email
     * @password
     */
    public function rules()
    {
        return [
            'password' => ['required', 'string', 'min:6'],
        ];
    }
}
