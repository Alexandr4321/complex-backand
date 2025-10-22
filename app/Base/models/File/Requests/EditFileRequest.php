<?php

namespace App\Base\Requests;

use App\Base\Models\File;
use App\System\Requests\Request;

class EditFileRequest extends Request
{
    public function rules()
    {
        return [
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
            'position' => [
                'nullable',
                'integer',
            ],
            'ownerId' => [
                'nullable',
                'integer',
            ],
            'ownerType' => [
                'nullable',
                'string',
            ],
            'isVerified' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
