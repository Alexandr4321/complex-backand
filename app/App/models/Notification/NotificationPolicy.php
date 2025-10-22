<?php

namespace App\App\Policies;

use App\App\Models\Faq;
use App\Auth\Models\User;

class NotificationPolicy
{
    public function read(User $user)
    {
        return true; //$user->hasGrant('moderator');
    }

    public function create(User $user)
    {
        return $user->isAdmin;
    }
}
