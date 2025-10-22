<?php

namespace App\Auth\Resources;

use App\App\Resources\AvatarResource;
use App\App\Resources\BranchResource;
use App\App\Resources\DepartmentResource;
use App\App\Resources\PassportResource;
use App\App\Resources\PositionsResource;
use App\System\Resources\JsonResource;

class UserResource extends JsonResource
{

    public function fields($model, $params)
    {
        return [
            'id' => $model->id,
            'firstName' => $model->firstName,
            'lastName' => $model->lastName ,

            'surName' => $model->surName,
            'complexId' => $model->complexId,
            'homeId' => $model->homeId,
            'entranceId' => $model->entranceId,
            'floorId' => $model->floorId,
            'apartmentId' => $model->apartmentId,
            'isAdmin' => $model->isAdmin,
            'isRegistered' => $model->isRegistered,
            'login' => $model->login ,
            'phone' => $model->phone ,
        ];
    }

    public function filters()
    {
        return [
            'isAdmin',

        ];
    }

    public function relations()
    {
        return [

        ];
    }
}
