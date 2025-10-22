<?php

namespace App\Auth\Types;

use App\System\Classes\Type;

class TypeUserRegister extends Type
{
    const required = [  'email', 'fullName', 'password', ];

    /** @var string */
    public $email;

    /** @var string */
    public $fullName;

    /** @var string */
    public $password;
}
