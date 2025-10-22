<?php

namespace App\App\Policies;

use App\App\Models\Apartment;
use App\App\Models\Auto;
use App\Auth\Models\User;

class AutoPolicy
{
    public function read(User $user)
    {
        return  true;//$user->hasGrant('moderator');
    }
    public function readAll(User $user)
    {
        return  true;
    }

    public function edit(User $user, Auto $template = null)
    {
        return  true;
    }
    public function create(User $user, Auto $template = null)
    {
        return  true;
    }

    public function addPhotos(User $user, Auto $model)
    {
        return $user->isAdmin || $user->id === $model->supplierId; //$user->hasGrant('moderator');
    }
}
