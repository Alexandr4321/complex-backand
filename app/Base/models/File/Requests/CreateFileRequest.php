<?php

namespace App\Base\Requests;

use App\Base\Models\File;
use App\System\Requests\Request;

class CreateFileRequest extends Request
{
    public function rules()
    {
        return [
            'file' => [
                'required',
                'file',
                'max:50000',
            ],
            'tag' => [
                'nullable',
                'string',
                'max:'.File::getMax('tag'),
            ],
            'name' => [
                'nullable',
                'string',
                'max:'.File::getMax('name'),
            ],
            'description' => [
                'nullable',
                'string',
                'max:'.File::getMax('description'),
            ],
        ];
    }
}
