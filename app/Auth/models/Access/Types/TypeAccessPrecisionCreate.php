<?php

namespace App\Auth\Types;

use App\System\Classes\Type;

class TypeAccessPrecisionCreate extends Type
{
    const required = [ 'type', ];
    
    /** @var integer */
    public $itemId = null;
    
    /** @var string */
    public $type;
}
