<?php

namespace App\Base\Models;

use App\System\Models\Model;

class History extends Model
{
    protected $table = 'base__history';
    
    protected $attributes = [
        'userId' => null,
        'accessId' => null,
        'targetId' => null,
        'targetType' => null,
        'data' => null,
        'ip' => null,
        'host' => null,
        'userAgent' => null,
    ];
    
    protected $casts = [
        'id' => 'integer',
        'userId' => 'integer',
        'accessId' => 'integer',
        'targetId' => 'integer',
        'targetType' => 'string',
        'action' => 'string',
        'data' => 'array',
        'ip' => 'string',
        'host' => 'string',
        'userAgent' => 'string',
        'createdAt' => 'datetime',
    ];
    
    
    
    public function target()
    {
        return $this->morphTo();
    }
    
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    
    public function access()
    {
        return $this->belongsTo(Permission::class, 'access_id');
    }
}
