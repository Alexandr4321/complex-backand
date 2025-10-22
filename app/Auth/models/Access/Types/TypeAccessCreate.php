<?php

namespace App\Auth\Types;

use App\System\Classes\Type;
use App\System\Models\Model;

class TypeAccessCreate extends Type
{
    const required = [ 'access', 'owner', ];
    
    /** @var Model */
    public $access;
    
    /** @var integer */
    public $modelId = null;
    
    /** @var Model */
    public $owner;
    
    /** @var Model */
    public $contractor = null;
    
    /** @var TypeAccessPrecisionCreate[] */
    public $precisions = [];
    
    
    /**
     * @param  array  $value
     * @return TypeAccessPrecisionCreate[]
     */
    public static function setPrecisions($value)
    {
        return array_map(function ($item) {
            return new TypeAccessPrecisionCreate($item);
        }, $value ?: []) ?: [];
    }
}
