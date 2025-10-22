<?php

namespace App\Base\Policies;

use App\Auth\Models\User;
use App\Base\Models\File;

class FilePolicy
{
    public function read(User $user)
    {
        return true; //$user->hasGrant('admin');
    }

    public function create(User $user)
    {
        return true; //$user->hasGrant('admin');
    }

    public function edit(User $user, User $model)
    {
        return true; //$user->hasGrant('admin');
    }

    public function delete(User $user, File $model)
    {
        return $model->authorId === $user->id; //$user->hasGrant('admin');
    }

}
