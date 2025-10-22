<?php

namespace App\System\Exceptions;

class NotFoundException extends BaseException
{
    protected $code = 404;
    
    protected $message = 'Page not found.';
}
