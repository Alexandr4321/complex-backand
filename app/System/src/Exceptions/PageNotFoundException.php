<?php

namespace App\System\Exceptions;

class PageNotFoundException extends BaseException
{
    protected $message = 'Page not found.';
}
