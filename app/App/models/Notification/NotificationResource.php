<?php

namespace App\App\Resources;

use App\Auth\Resources\UserResource;
use App\System\Resources\JsonResource;

class NotificationResource extends JsonResource
{
    public function fields($model, $params)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'content' => $model->content,
            'isRead' => $model->isRead,
            'route' => $model->route,
            'modelId' => $model->modelId,
            'userId' => $model->userId,
            'createdAt' => $model->createdAt,
        ];
    }

    public function filters()
    {
        return [
        ];
    }

    public function relations()
    {
        return [];
    }
}
