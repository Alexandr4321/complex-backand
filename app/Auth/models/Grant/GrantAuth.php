<?php

namespace App\Auth\Models;

/**
 * Используется для доставания и каста грантов через access
 */
class GrantAuth extends Grant
{
    protected $casts = [
        'name' => 'string',
        'modelId' => 'integer',
        'isGlobal' => 'boolean'
    ];
}
