<?php

namespace App\Auth\Helpers;

use App\Auth\Models\Invite;
use App\System\Exceptions\BusinessException;
use App\System\Exceptions\ServerException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class InviteService
{
    /**
     * @param  Invite|Model  $invite
     * @return bool
     */
    public static function checkInvite($invite, $type)
    {
        if (!$invite) {
            throw new BusinessException('Приглашение не найдено');
        } elseif ($invite->sendAt > Carbon::now()->addWeek()) {
            throw new BusinessException('Приглашение устарело');
        }
        
        if ($invite->invitedType !== Invite::invitedTypes[$type]) {
            throw new ServerException('Invite type is invalid');
        }
        
        return true;
    }
}
