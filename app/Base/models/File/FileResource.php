<?php

namespace App\Base\Resources;

use App\Auth\Resources\UserResource;
use App\System\Resources\JsonResource;

class FileResource extends JsonResource
{

    public function fields($model, $params)
    {
        return [
            'id' => $model->id,
            'authorId' => $model->authorId,
            'author' => $this->when($model->relationLoaded('author'), new UserResource($model->author)),
            'tag' => $model->tag,
            'name' => $model->name,
            'description' => $model->description,
            'size' => $model->size,
            'src' => $model->url,
            'isExternal' => $model->isExternal,
            'isVerified' => $model->isVerified,
            'position' => $model->position,
            'createdAt' => $model->createdAt,
        ];
    }
}
