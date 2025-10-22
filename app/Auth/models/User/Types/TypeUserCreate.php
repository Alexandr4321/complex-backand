<?php

namespace App\Auth\Types;

use App\System\Classes\Type;

class TypeUserCreate extends Type
{
    const required = [  'email', 'fullName', ];


    /** @var string */
    public $email;

    /** @var string */
    public $fullName;
}
