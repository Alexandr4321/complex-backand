<?php

namespace App\App\Models;

use App\App\Notifications\PushNotify;
use App\System\Exceptions\BaseException;
use App\System\Exceptions\ValidationException;
use App\System\Models\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;

class Push extends Model
{
    protected $table = 'app__fcm_pushes';

    use Notifiable;

    protected $casts = [
        'id' => 'integer',
        'userId' => 'integer',

        'fcmToken' => 'string',

        'createdAt' => 'datetime',
    ];

    /**
     * Specifies the user's FCM token
     *
     * @return string|array
     */
    public function routeNotificationForFcm()
    {
        return $this->fcmToken;
    }

    /**************
     * Operations *
     **************/

    /**
     * @param array $data
     * @return Push
     */
    public static function create($data)
    {
        $model = new self();

        Push::query()->where('fcmToken', $data['fcmToken'])->delete();

        $model->userId = $data['userId'];
        $model->fcmToken = $data['fcmToken'];
        $model->save();

        return $model;
    }


    public static function sendNotifications($user, $data)
    {

        if ($user->isPush) {
            $addresses = Push::query()->where('userId', $user->id)->get();
            if (!Arr::get($data, 'title')) {
                throw new ValidationException('Field title is required for sending a push notifications');
            }
            $sended = [];
            foreach ($addresses as $address) {
                if (!in_array($address->fcmToken, $sended)) {
                    $sended[] = $address->fcmToken;
                    if (Arr::get($data, 'content')) {
                        $converted = strtr($data['content'], array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES)));
                        $clear['content'] = trim($converted, chr(0xC2).chr(0xA0));
                    } else {
                        $clear['content'] = '';
                    }
                    $converted = strtr($data['title'], array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES)));
                    $clear['title'] = trim($converted, chr(0xC2).chr(0xA0));
                    try {
                        $address->notify(new PushNotify($clear['title'], $clear['content']));
                    } catch (\Exception $e) {
                        $address->delete();
                    }
                }
            }
        }
    }

    /**
     * @return bool|null
     */
    public function delete()
    {
        return parent::delete();
    }
}
