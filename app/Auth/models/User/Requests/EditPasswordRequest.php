<?php

namespace App\Auth\Requests;

use App\Auth\Models\User;
use App\Base\Models\File;
use App\System\Requests\Request;

class EditPasswordRequest extends Request
{
    public function rules()
    {
        return [
            'oldPass' => [
                'required',
                'string',
            ],
            'newPass' => [
                'required',
                'string',
            ],
            'confirmPass' => [
                'required',
                'string',
            ],
        ];
    }
}
