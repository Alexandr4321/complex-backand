<?php

namespace App\App\Resources;


use App\Base\Resources\FileResource;
use App\System\Resources\JsonResource;

class NewsResource extends JsonResource
{
    public function fields($model, $params)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'content' => $model->content,
            'complexId' => $model->complexId,
            'active' => $model->active,
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
            'complex' => $this->relation(ComplexResource::class),
            'images' => $this->relation(FileResource::class),
        ];
    }
}
