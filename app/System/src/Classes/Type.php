<?php

namespace App\System\Classes;

use App\System\Exceptions\ServerException;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Type implements \JsonSerializable
{
    const required = [];
    const missing = [];
    
    /**
     * Type constructor.
     * @param  array  $data
     */
    public function __construct($data = [])
    {
        foreach ($this::required as $key) {
            if (!isset($data[$key]) || $data[$key] === null || is_missed($data[$key])) {
                throw new ServerException("Field with name $key is required");
            }
        }
        foreach ($this::missing as $key) {
            if (!isset($data[$key])) {
                $data[$key] = new MissingValue();
                $this->{$key} = $data[$key];
            }
        }
        
        foreach (get_object_vars($this) as $key => $default) {
            $value = Arr::get($data, $key, $default);
            if (is_missed($value)) {
                continue;
            }
            if (!is_null($default) && is_null($value)) {
                $value = $default;
            }
            $setMethodName = 'set'.Str::ucfirst($key);
            if (method_exists($this, $setMethodName)) {
                $this->{$key} = $this::{$setMethodName}($value);
            } else {
                $this->{$key} = $value;
            }
        }
    }
    
    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];
        foreach (get_object_vars($this) as $key => $value) {
            if (!is_a($value, MissingValue::class) && !is_null($value)) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
    
    /**
     * @return string
     */
    public function toString()
    {
        return strVal($this->toArray());
    }
    
    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
