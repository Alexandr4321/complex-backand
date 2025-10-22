<?php

namespace App\App\Resources;

use App\Auth\Resources\UserResource;
use App\Base\Resources\FileResource;
use App\System\Resources\JsonResource;


class AutoResource extends JsonResource
{
    public function fields($model, $params)
    {
        return [
            'id' => $model->id,
            'userId' => $model->userId,
            'number' => $model->number,
            'brand' => $model->brand,
            'parkingSpace' => $model->parkingSpace,
            'createdAt' => $model->createdAt,
        ];
    }

    public function filters()
    {
        return [
            'title' => $this->scope('string', 'byTitle'),
        ];
    }

    public function relations()
    {
        return [
            'user' => $this->relation(UserResource::class),
        ];
    }
}
