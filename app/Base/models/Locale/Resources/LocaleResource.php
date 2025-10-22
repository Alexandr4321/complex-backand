<?php

namespace App\Eq\Resources;

use App\Base\Resources\FileResource;
use App\System\Resources\JsonResource;

class LocaleResource extends JsonResource
{
    public function fields($model, $params)
    {
        return [
            'name' => $model->name,
            'title' => $model->title,
            'isHidden' => $model->isHidden,
        ];
    }
}
