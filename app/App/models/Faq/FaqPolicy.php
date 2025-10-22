<?php

namespace App\App\Policies;

use App\App\Models\Faq;
use App\Auth\Models\User;

class FaqPolicy
{
    public function read(User $user)
    {
        return true; //$user->hasGrant('moderator');
    }

    public function edit(User $user, Faq $template = null)
    {
        return true; //$user->hasGrant('moderator');
    }

    public function create(User $user)
    {
        return true; //$user->hasGrant('moderator');
    }

    public function delete(User $user, Faq $template = null)
    {
        return true; //$user->hasGrant('moderator');
    }
}
