<?php

namespace App\Auth\Resources;

use App\System\Resources\JsonResource;

class AccessResource extends JsonResource
{
    public function fields($model, $params)
    {
        return $model->toArray();
    }
}
