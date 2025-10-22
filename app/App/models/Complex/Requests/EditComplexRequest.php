<?php

namespace App\App\Requests;

use App\System\Requests\Request;

class EditComplexRequest extends Request
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
