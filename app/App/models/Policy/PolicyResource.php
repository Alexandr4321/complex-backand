<?php

namespace App\App\Resources;

use App\Base\Resources\FileResource;
use App\System\Resources\JsonResource;

class PolicyResource extends JsonResource
{
    public function fields($model, $params)
    {
        return [
            'id' => $model->id,
            'content' => $model->content,
            'createdAt' => $model->createdAt,
        ];
    }

    public function relations()
    {
        return [
        ];
    }
}
