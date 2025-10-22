<?php

namespace App\App\Resources;

use App\Auth\Resources\UserResource;
use App\Base\Resources\FileResource;
use App\System\Resources\JsonResource;

class HomeResource extends JsonResource
{
    public function fields($model, $params)
    {
        return [
            'id' => $model->id,
            'complexId' => $model->complexId,
            'title' => $model->title,
            'content' => $model->content,
            'elevator' => $model->elevator,
            'address' => $model->address,
            'createdAt' => $model->createdAt,
        ];
    }

    public function filters()
    {
        return [
            'title' => $this->scope('string', 'byTitle'),
            'type' => $this->scope('string', 'ByType'),
            'price' => $this->scope('string', 'ByPrice'),
        ];
    }

    public function relations()
    {
        return [
            'user' => $this->relation(UserResource::class),
            'images' => $this->relation(FileResource::class),
        ];
    }
}
