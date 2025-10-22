<?php

namespace App\System\Exceptions;

class ModelNotFoundException extends BaseException
{
    protected $message = 'Model not found.';
}
