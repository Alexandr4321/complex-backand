<?php

namespace App\App\Controllers\Api;

use App\App\Models\Notification;
use App\App\Requests\CreateNotificationRequest;
use App\App\Resources\NotificationResource;
use App\System\Controllers\Controller;


class NotificationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @title Получить
     * @alias api.notifications.get
     * @path notifications/{notification}
     */
    public function get(Notification $model)
    {
        $this->authorize('read', [Notification::class,]);
        return $this->response(new NotificationResource($model));
    }

    /**
     * @title Получить список
     * @alias api.notifications.getList
     * @path notifications
     */
    public function getList()
    {

        $this->authorize('read', [Notification::class,]);
        $models = Notification::query()->where('userId', auth()->user()->id);
        $query = clone $models;
        $query->update(['isRead' => true]);

        return $this->response(new NotificationResource($models));
    }

    /**
     * @title Получить список
     * @alias api.notifications.getOnlyNewList
     * @path new/notifications
     */
    public function getOnlyNewList()
    {

        $this->authorize('read', [Notification::class,]);
        $models = Notification::query()->where('userId', auth()->user()->id)->where('isRead', '!=', true);

        return $this->response(new NotificationResource($models));
    }


    /**
     * @title Создать
     * @alias notifications.post
     * @path notifications
     */
    public function post(CreateNotificationRequest $request)
    {

        $this->authorize('read', [Notification::class,]);

        $request->validate();

        $model = Notification::create(request()->all());

        return $this->response(new NotificationResource($model));
    }


}
