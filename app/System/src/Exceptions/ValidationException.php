<?php

namespace App\System\Exceptions;

class ValidationException extends BaseException
{
    protected $code = 422;
    
    protected $message = 'Validation errors.';
}
