<?php

namespace App\System\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Collection as BaseCollection;

class Model extends BaseModel
{
    use PatchModel;
    
    const CREATED_AT = 'createdAt';
    const DELETED_AT = 'deletedAt';
    const UPDATED_AT = null;
    
    // integer, real, float, double, decimal:<digits>, string, boolean, object, array, collection, date, datetime, timestamp
    protected $casts = [];
    
    
    /**
     * @param  string  $type
     * @param  mixed  $value
     * @return mixed
     */
    public static function cast($type, $value)
    {
        if (is_null($value)) {
            return $value;
        }
        
        $self = (new self);
        
        switch ($type) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return $self->fromFloat($value);
            case 'decimal':
                return $self->asDecimal($value, 2);
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'object':
                return $self->fromJson($value, true);
            case 'array':
            case 'json':
                return $self->fromJson($value);
            case 'collection':
                return new BaseCollection($self->fromJson($value));
            case 'date':
                return $self->asDate($value);
            case 'datetime':
            case 'custom_datetime':
                return $self->asDateTime($value);
            case 'timestamp':
                return $self->asTimestamp($value);
        }
    
        return $value;
    }
    
    
    /**
     * todo temp fix localized
     * @param  string  $key
     * @return mixed|void
     */
    public function getAttribute($key)
    {
        if (! $key) {
            return;
        }
        
        // If the attribute exists in the attribute array or has a "get" mutator we will
        // get the attribute's value. Otherwise, we will proceed as if the developers
        // are asking for a relationship's value. This covers both types of values.
        if (array_key_exists($key, $this->attributes) ||
            array_key_exists($key, $this->casts) ||
            $this->hasGetMutator($key) ||
            $this->isClassCastable($key) ||
            array_key_exists($key, $this->getAttributes())) {
            return $this->getAttributeValue($key);
        }
        
        // Here we will determine if the model base class itself contains this given key
        // since we don't want to treat any of those methods as relationships because
        // they are all intended as helper methods and none of these are relations.
        if (method_exists(self::class, $key)) {
            return;
        }
        
        return $this->getRelationValue($key);
    }
}
