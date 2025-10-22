<?php

namespace App\App\Policies;

use App\App\Models\Complex;
use App\Auth\Models\User;

class ComplexPolicy
{
    public function read(User $user)
    {
        return  true;
    }
    public function readAll(User $user)
    {
        return  true;
    }

    public function edit(User $user, Complex $template = null)
    {
        return  true;
    }
    public function create(User $user, Complex $template = null)
    {
        return  true;
    }

    public function addPhotos(User $user, Complex $model)
    {
        return $user->isAdmin || $user->id === $model->supplierId;
    }
}
