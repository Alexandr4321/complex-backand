<?php

namespace App\App\Requests;

use App\App\Models\Product;
use App\System\Requests\Request;

class UpdateFaqRequest extends Request
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
