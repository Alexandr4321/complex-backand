<?php

namespace App\Auth\Models;

use App\Eq\Models\Section;
use App\Eq\Models\WorkType;
use App\System\Models\Model;

/**
 */
class AccessPrecision extends Model
{
    protected $table = 'auth__access_precisions';

    public const CREATED_AT = null;
    
    protected $casts = [
        'id' => 'integer',
        'accessId' => 'integer',
        'itemId' => 'integer',
        'type' => 'string', // values from self::types
    ];
    
    public const max = [
        'dataType' => 63,
    ];
    
    public const types = [
        'section' => Section::class,
        'workType' => WorkType::class,
    ];
}
