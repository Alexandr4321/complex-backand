<?php

namespace App\Auth\Models;

use App\System\Models\Pivot;

/**
 *
 */
class RolePermit extends Pivot
{
    protected $table = 'auth__roles_permits';
    
    protected $casts = [
        'roleId' => 'integer',
        'permitId' => 'integer',
    ];
}
