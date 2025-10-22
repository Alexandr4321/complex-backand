<?php

namespace App\App\Policies;

use App\App\Models\Card;
use App\App\Models\Home;
use App\App\Models\News;
use App\App\Models\Product;
use App\Auth\Models\User;

class NewsPolicy
{
    public function read(User $user)
    {
        return  true;//$user->hasGrant('moderator');
    }
    public function readAll(User $user)
    {
        return  true;
    }

    public function edit(User $user, News $template = null)
    {
        return  true;
    }
    public function create(User $user, News $template = null)
    {
        return  true;
    }

    public function addPhotos(User $user, News $model)
    {
        return $user->isAdmin || $user->id === $model->supplierId; //$user->hasGrant('moderator');
    }
}
