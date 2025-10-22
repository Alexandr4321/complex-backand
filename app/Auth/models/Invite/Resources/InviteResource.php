<?php

namespace App\Auth\Resources;

use App\Auth\Models\Invite;
use App\Company\Resources\CompanyResource;
use App\System\Resources\JsonResource;

class InviteResource extends JsonResource
{
    public function fields($model, $params)
    {
        $resources = [
            'user' => UserResource::class,
            'company' => CompanyResource::class,
        ];
        
        $invitedType = array_search($model->invitedType, Invite::invitedTypes);
        $inviterType = array_search($model->inviterType, Invite::inviterTypes);
        
        return [
            'invited' => new ($resources[$invitedType]),
            'invitedType' => $invitedType,
            'token' => $model->token,
            'inviter' => new ($resources[$inviterType]),
            'inviterType' => $inviterType,
            'sendAt' => $model->sendAt,
            'createdAt' => $model->createdAt,
        ];
    }
    
    public function relations()
    {
        return [];
    }
}
