<?php

namespace App\App\Resources;


use App\Auth\Resources\UserResource;
use App\System\Resources\JsonResource;


class ComplexResource extends JsonResource
{
    public function fields($model, $params)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'complexAdminId' => $model->complexAdminId,
            'parkingSpaces' => $model->parkingSpaces,
            'phone' => $model->phone,
            'email' => $model->email,
            'instagram' => $model->instagram,
            'whatsapp' => $model->whatsapp,
            'address' => $model->address,
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
            'administrator' => $this->relation(UserResource::class),
        ];
    }
}
