<?php

namespace App\System\Exceptions;

class AuthenticationException extends BaseException
{
    protected $code = 401;
    
    protected $message = 'You must log in.';
}
