<?php

namespace App\Eq\Policies;

use App\Auth\Models\User;

class LocalePolicy
{
    public function read(User $user)
    {
        return true;
    }
    
    public function readActive(User $user)
    {
        return true;
    }
}
