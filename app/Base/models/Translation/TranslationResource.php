<?php

namespace App\Base\Resources;

use App\System\Resources\JsonResource;

class TranslationResource extends JsonResource
{

    public function fields($model, $params)
    {
        return [
            'id' => $model->id,
            'field' => $model->field,
            'value' => $model->value,
            'lvl' => $model->lvl,
        ];
    }
}
