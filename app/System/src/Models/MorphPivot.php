<?php

namespace App\System\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot as BaseMorphPivot;

class MorphPivot extends BaseMorphPivot
{
    use PatchModel;
    
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    
    public $incrementing = true;
    
    protected $guarded = [];

    // integer, real, float, double, decimal:<digits>, string, boolean, object, array, collection, date, datetime, timestamp
    protected $casts = [];
}
