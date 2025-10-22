<?php

namespace App\App\Policies;

use App\App\Models\Apartment;
use App\Auth\Models\User;

class ApartmentPolicy
{
    public function read(User $user)
    {
        return  true;//$user->hasGrant('moderator');
    }
    public function readAll(User $user)
    {
        return  true;
    }

    public function edit(User $user, Apartment $template = null)
    {
        return  true;
    }
    public function create(User $user, Apartment $template = null)
    {
        return  true;
    }

    public function addPhotos(User $user, Apartment $model)
    {
        return $user->isAdmin || $user->id === $model->supplierId; //$user->hasGrant('moderator');
    }
}
