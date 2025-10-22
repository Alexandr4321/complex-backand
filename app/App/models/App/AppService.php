<?php

namespace App\Auth\Helpers;

use App\App\ApiRequest;


use Mobizon\MobizonApi;


class AppService
{
    /**
     */

    const monthes =
        [
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь',
        ];

    public static function sendSms($phone, $content)
    {
        $phone = UserService::clearPhone($phone);
        $phone = str_replace('+', '', $phone);

        $api = new MobizonApi(config('sms.apiKey'), 'api.mobizon.kz');
        $api->call('message', 'sendSMSMessage',
            array(
                'recipient' => $phone,
                'text' => $content . ' kexFasM5xcS',
                )
            );

    }


}
