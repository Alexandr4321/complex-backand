<?php

namespace App\Auth\Controllers\Api;

use App\Auth\Helpers\InviteService;
use App\Auth\Models\Invite;
use App\Auth\Resources\InviteResource;
use App\System\Controllers\Controller;
use App\System\Exceptions\BusinessException;
use App\System\Requests\Request;
use Carbon\Carbon;

/**
 * @group Auth / Invite
 */
class InviteController extends Controller
{
    /**
     * @title Получить информацию о приглашении по токену
     * @alias auth.invite.get
     * @path auth/invite/{token}
     */
    public function getInfo(Request $request, $token)
    {
        $invite = Invite::query()->where('token', $token)->first();
    
        InviteService::checkInvite($invite);
    
        return $this->response(new InviteResource($invite));
    }
}
