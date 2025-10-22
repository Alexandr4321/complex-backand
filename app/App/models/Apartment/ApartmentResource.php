<?php

namespace App\App\Resources;

use App\Auth\Resources\UserResource;
use App\Base\Resources\FileResource;
use App\System\Resources\JsonResource;


class ApartmentResource extends JsonResource
{
    public function fields($model, $params)
    {
        return [
            'id' => $model->id,
            'userId' => $model->userId,
            'number' => $model->number,
            'apartmentArea' => $model->apartmentArea,
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
