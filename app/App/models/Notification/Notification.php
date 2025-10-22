<?php

namespace App\App\Models;

use App\Auth\Models\User;
use App\System\Models\Model;
use Illuminate\Support\Arr;

class Notification extends Model
{
    protected $table = 'app__notifications';

    protected $attributes = [
        'content' => null,
        'isRead' => false,
    ];

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'content' => 'string',
        'isRead' => 'boolean',
        'userId' => 'integer',
        'createdAt' => 'datetime',
    ];

    public const max = [
        'title' => 225,
        'content' => 225
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    public static function  create($data)
    {
        $model = new self();
        $model->userId = $data['userId'];
        $model->title = $data['title'];
        $model->route = Arr::get($data, 'route');
        $model->modelId = Arr::get($data, 'modelId');
        $model->content = Arr::get($data, 'content');
        $model->save();


        $user = User::query()->find($model->userId);
        if ($user) {
            Push::sendNotifications($user, [
                'title' => $model->title,
                'content' => $model->content,
            ]);
        }

        return $model;
    }
}












