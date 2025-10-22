<?php

namespace App\System\Exceptions;

class ServerException extends BaseException
{
    protected $code = 500;
    
    protected $message = 'Unknown server error.';
}
