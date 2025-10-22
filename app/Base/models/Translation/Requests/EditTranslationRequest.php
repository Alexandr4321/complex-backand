<?php

namespace App\Base\Requests;

use App\System\Requests\Request;

class EditTranslationRequest extends Request
{
    public function rules()
    {
        return [
            'value' => [
                'nullable',
                'string',
            ],
        ];
    }
}
