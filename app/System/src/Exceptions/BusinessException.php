<?php

namespace App\System\Exceptions;

class BusinessException extends BaseException
{
    protected $code = 422;
    
    protected $message = 'Business logic errors.';
}
