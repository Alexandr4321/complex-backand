<?php

namespace App\System\Exceptions;

class AuthorizationException extends BaseException
{
    protected $code = 403;
    
    protected $message = 'Access denied.';
}
