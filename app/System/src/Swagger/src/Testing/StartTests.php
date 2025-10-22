<?php

namespace App\System\Swagger\Services;

use Illuminate\Support\Facades\Artisan;

class StartTests
{

    private static $started = false;

    public static function start()
    {
        if (!StartTests::$started) {
            Artisan::call('config:clear');
            Artisan::call('migrate:refresh');
            Artisan::call('db:seed');

//            StartTests::$started = true;
        }
    }
}
