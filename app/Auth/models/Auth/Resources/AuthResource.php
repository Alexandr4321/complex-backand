<?php

namespace App\Auth\Resources;

use App\Auth\Service\AccessService;
use App\System\Resources\JsonResource;
use Illuminate\Support\Arr;

class AuthResource extends JsonResource
{
    public function fields($model, $params)
    {
        return [
            'user' => new UserResource($model),
            'token' => Arr::get($params, 'token'),
            'grants' => AccessService::getGrants($model),
        ];
    }

    public function relations()
    {
        return [];
    }
}
