<?php

namespace App\Auth\Policies;

use App\Auth\Models\User;

class UserPolicy
{
    public function readOne(User $user, User $model)
    {
        return true; //$user->hasGrant('admin');
    }

    public function read(User $user)
    {
        return true; //$user->hasGrant('admin');
    }

    public function create(User $user)
    {
        return $user->hasGrant('admin');
    }

    public function edit(User $user, User $model)
    {
        return $user->hasGrant('admin') || $user->id === $model->id;
    }

    public function deleteAcc(User $user, User $model)
    {
        return $user->hasGrant('admin') || $user->id === $model->id;
    }

    public function delete(User $user, User $model)
    {
        return $user->hasGrant('admin');
    }

    public function editPass(User $user, User $model)
    {
        return $user->hasGrant('admin') ?: $user->id === $model->id;
    }
}
