<?php

namespace App\App\Requests;

use App\App\Models\Order;
use App\App\Models\Tractor;
use App\Auth\Models\User;
use App\System\Requests\Request;

class EditPolicyRequest extends Request
{
    public function rules()
    {
        return [
            'truckId' => [
                'string',
            ],
        ];
    }
}
