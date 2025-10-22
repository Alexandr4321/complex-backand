<?php

namespace App\Base\Policies;

use App\Auth\Models\User;
use App\Base\Models\Translation;

class TranslationPolicy
{
    public function read(User $user)
    {
        return true;
    }
    
    public function edit(User $user, Translation $template = null)
    {
        return true;
    }
}
