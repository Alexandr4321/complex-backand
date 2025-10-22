<?php

namespace App\App\Requests;

use App\App\Models\Faq;
use App\System\Requests\Request;

class SaveFcmPushRequest extends Request
{
    public function rules()
    {
        return [
            'fcmToken' => [
                'required',
                'string',
            ],
        ];
    }
}
