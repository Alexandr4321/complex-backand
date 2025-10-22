<?php

namespace App\Base\Models;

use App\System\Models\Model;

class TranslationCustom extends Model
{
    public const UPDATED_AT = null;
    public const CREATED_AT = null;
    
    protected $primaryKey = [ 'id', 'localeId' ];
    
    public $incrementing = false;
    
    protected $casts = [
        'localeId' => 'integer',
    ];
}
