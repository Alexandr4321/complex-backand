<?php

namespace App\System\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

class Pivot extends BaseModel
{
    use PatchModel, AsPivot {
        AsPivot::setKeysForSaveQuery insteadof PatchModel;
    }

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    
    public $incrementing = true;
    
    protected $guarded = [];

    // integer, real, float, double, decimal:<digits>, string, boolean, object, array, collection, date, datetime, timestamp
    protected $casts = [];
}
