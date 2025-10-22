<?php

namespace App\Auth\Models;

use App\System\Models\Pivot;

/**
 */
class PermitGrant extends Pivot
{
    protected $table = 'auth__permits_grants';
    
    public const CREATED_AT = null;

    protected $attributes = [
        'isGlobal' => false,
    ];
    
    protected $casts = [
        'permitId' => 'integer',
        'grantId' => 'integer',
        'isGlobal' => 'boolean',
    ];
}
