<?php

namespace App\App\Controllers\Api;

use App\App\Models\Push;
use App\App\Requests\SaveFcmPushRequest;
use App\App\Resources\PushResource;
use App\Auth\Models\User;
use App\System\Controllers\Controller;
use App\System\Requests\Request;

class PushController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except([
            'getTranslations',
        ]);
    }

    /**
     * @title сохранить токен
     * @alias api.app.post
     * @path fcm-push/save
     */
    public function post(SaveFcmPushRequest $request)
    {
        $request->validate();

        $data = request()->all();
        $user = User::query()->find(auth()->user()->id);
        $data['userId'] = $user->id;

        $existedModel = Push::query()->where('fcmToken', $data['fcmToken'])->first();

        if ($existedModel) {
            $existedModel->delete();
        }

        $model = Push::create($data);
        return $this->response(new PushResource($model));
    }

    /**
     * @title сохранить токен
     * @alias api.app.get
     * @path fcm-push/users/{user}
     */
    public function get(Request $request, User $user)
    {
        $request->validate();

        $model = Push::query()->where('userId', $user->id);
        return $this->response(new PushResource($model));
    }

    /**
     * @title delete
     * @alias api.app.delete
     * @path fcm-push/delete
     */
    public function delete(Request $request)
    {
        $request->validate();
        Push::query()->where('fcmToken', request('fcmToken'))->delete();

        return $this->response(['res' => 'Удалено', 'toke' => request('fcmToken')]);
    }
}
