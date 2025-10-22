<?php

namespace App\Auth\Types;

use App\System\Classes\Type;

class TypeRoleCreate extends Type
{
    const required = [ 'name', ];
    
    /** @var string */
    public $name;
    
    /** @var string */
    public $modelType = null;
    
    /** @var string|string[]string */
    public $description = null;
}
