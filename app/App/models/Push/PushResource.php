<?php

namespace App\App\Resources;

use App\Auth\Resources\UserResource;
use App\System\Resources\JsonResource;

class PushResource extends JsonResource
{
    public function fields($model, $params)
    {
        return [
            'id' => $model->id,
            'userId' => $model->userId,
            'companyId' => $model->companyId,
            'fcmToken' => $model->fcmToken,
            'createdAt' => $model->createdAt,
        ];
    }
}
