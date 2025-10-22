<?php

namespace App\Auth\Requests;

use App\Auth\Models\Access;
use App\Auth\Models\AccessPrecision;
use App\System\Requests\Request;
use Illuminate\Support\Arr;

class CreateAccessRequest extends Request
{
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                $this->ruleExists(Arr::get(Access::types, request('type')), 'name'),
            ],
            'type' => [
                'required',
                'string',
                'in:'.implode(',', array_keys(Access::types)),
            ],
            'modelId' => [
                'integer',
            ],
            'ownerId' => [
                'required',
                'integer',
                $this->ruleExists(Arr::get(Access::ownerTypes, request('ownerType')), 'id'),
            ],
            'ownerType' => [
                'required',
                'string',
                'in:'.implode(',', array_keys(Access::ownerTypes)),
            ],
            
            'data' => [
                'array'
            ],
            'data.0.dataId' => [
                'integer',
            ],
            'data.0.dataType' => [
                'string',
                'in:'.implode(',', array_keys(AccessPrecision::types)),
            ],
        ];
    }
}
