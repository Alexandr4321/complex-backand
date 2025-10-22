<?php

namespace App\System\Exceptions;

class TokenMismatchException extends BaseException
{
    protected $code = 419;
    
    protected $message = 'Token incorrect.';
}
