<?php

namespace App\App\Requests;

use App\System\Requests\Request;

class CreateNotificationRequest extends Request
{
    public function rules()
    {
        return [
            'title' => [
                'required',
                'string'
            ],
            'userId' => [
                'required',
                'integer'
            ],
            'content' => [
                'nullable',
                'string'
            ]
        ];
    }
}
