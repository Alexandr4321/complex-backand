<?php

namespace App\App\Requests;


use App\System\Requests\Request;

class CreateComplexRequest extends Request
{
    public function rules()
    {
        return [
            'title' => [
                'required',
                'string',
            ],
        ];
    }
}
