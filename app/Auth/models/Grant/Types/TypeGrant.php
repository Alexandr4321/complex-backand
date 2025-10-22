<?php

namespace App\Auth\Types;

use App\System\Classes\Type;

class TypeGrant extends Type
{
    const required = [ 'name', ];
    
    /** @var string */
    public $name;
    
    /** @var string */
    public $modelType = null;
}
