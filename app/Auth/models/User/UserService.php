<?php

namespace App\Auth\Helpers;

use Illuminate\Support\Str;

class UserService
{
    /**
     * @return string
     */
    public static function createEmailVerifyToken()
    {
        return Str::uuid();
    }

    /**
     * @return int
     */
    public static function createPhoneVerifyToken()
    {
        return rand(1000, 9999); // 4 digits
    }

    /**
     * @param  string  $password
     * @return string
     */
    public static function cryptPassword($password)
    {
        return bcrypt($password);
    }



    /**
     * @param  string  $phone
     * @return string
     */
    public static function clearPhone($phone)
    {
        $result = preg_replace('/[^0-9]/', '', $phone);

        if (substr($result, 0, 1) === '8') {
            $result = '+7'.substr($result, 1);
        } elseif (substr($result, 0, 1) === '7') {
            $result = '+'.$result;
        }

        return $result;
    }
}
