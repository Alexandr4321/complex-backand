<?php

namespace App\App\Resources;

use App\System\Resources\JsonResource;

class FaqResource extends JsonResource
{

    public function fields($model, $params)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'content' => $model->content,
            'createdAt' => $model->createdAt,
        ];
    }
}
