<?php

namespace App\App\Requests;

use App\App\Models\Product;
use App\System\Requests\Request;

class CreateQuizRequest extends Request
{
    public function rules()
    {
        return [
            'title' => [
                'required',
                'string',
            ],
            'content' => [
                'required',
                'string',
            ],
        ];
    }
}
