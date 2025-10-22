<?php

namespace App\System\Models;

use App\System\Classes\Type;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Arr;
use App\System\Models\Traits\HasBelongsToThrough;

trait PatchModel
{
    use HasBelongsToThrough;
    
    protected static function attributes()
    {
        return [];
    }
    
    /**
     * @param  array  $data
     * <code>
     * $data = [
     *
     * ];
     * </code>
     *
     * @return self
     */
    public static function create($data)
    {
        return new self();
    }
    
    /**
     * @param  array  $data
     * <code>
     * $data = [
     *
     * ];
     * </code>
     *
     * @return self
     */
    public function edit($data)
    {
        return $this;
    }
    
    /**
     * Delete all items of given relation
     *
     * @param  string  $relation
     */
    public function deleteRelationList($relation)
    {
        foreach ($this->{$relation} as $item) {
            $item->delete();
        }
    }
    
    /**
     * Set fields if its exist in $data
     *
     * @param  array  $fields
     * @param  array|Type  $data
     */
    protected function postFields($fields, $data)
    {
        if (is_a($data, Type::class)) {
            $data = $data->toArray();
        }
        foreach ($fields as $fieldName) {
            if (!is_missed($field = Arr::get($data, $fieldName, new MissingValue()))) {
                $this->{$fieldName} = $field;
            }
        }
    }
    
    public static function getMax($field)
    {
        // todo разобраться почему при вызове Company::getMax() self показывает Model
        $defaultMax = 25500;
        //        if (defined('self::max')) {
        //            return Arr::get(self::max, $field, $defaultMax);
        //        }
        return $defaultMax;
    }
    
    /**
     * Define a custom relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @param  Closure  $base  // function(Builder $query)
     * @param  Closure  $matcher  // function(array $models, Collection $results, string $relation, CustomRelation $instance)
     * @param  Closure  $toResult  // function(array $models)
     * @return CustomRelation
     */
    public function relation($related, $foreignKey, $localKey, $base = null, $matcher = null, $toResult = null)
    {
        /** @var Model $instance */
        $instance = new $related;
        $query = $instance->newQuery();
        
        if (is_callable($base)) {
            $base($query);
        }
        
        return new CustomRelation($query, $this, $foreignKey, $localKey, $matcher, $toResult);
    }
    
    /**
     * Define a custom relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @param  Closure  $base  // function(Builder $query)
     * @param  Closure  $matcher  // function(array $models, Collection $results, string $relation, CustomRelation $instance)
     * @param  Closure  $toResult  // function(array $models)
     * @return CustomRelation
     */
    public function relationOne($related, $foreignKey, $localKey, $base = null, $matcher = null, $toResult = null)
    {
        /** @var Model $instance */
        $instance = new $related;
        $query = $instance->newQuery();
        
        if (is_callable($base)) {
            $base($query);
        }
        
        return new CustomRelationOne($query, $this, $foreignKey, $localKey, $matcher, $toResult);
    }
    
    
    /****************************
     *      Laravel rewrite     *
     ****************************/
    
    /**
     * Patch, using array of keys as primary.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }
        
        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }
        
        return $query;
    }
    
    /**
     * Patch, using array of keys as primary.
     *
     * @param  mixed  $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }
        
        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }
        
        return $this->getAttribute($keyName);
    }
    
    /****************************
     *    Laravel rewrite end   *
     ****************************/
    
    // Помогает кастовать любой формат даты как в Validation::date
    public function asDateTime($value)
    {
        if (is_string($value)) {
            $value = date($this->getDateFormat(), strtotime($value));
        } elseif (is_int($value)) {
            $value = date($this->getDateFormat(), $value);
        }
        
        return parent::asDateTime($value);
    }
    
    // TODO Перепроверить
    
    protected $cascadeRelations = [];
    
    /**
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $attrs = $this->attributes();
        foreach ($attrs as $key => $value) {
            if (!isset($this->attributes[$key])) {
                $this->attributes[$key] = $value;
            }
        }
        
        $relations = [];
        
        foreach ($this->cascadeRelations as $relationName) {
            if (isset($this->attributes[$relationName])) {
                $relations[$relationName] = $this->attributes[$relationName];
                unset($this->attributes[$relationName]);
            }
        }
        
        $result = parent::save($options);
        
        foreach ($relations as $relationName => $value) {
            $this->saveRelation($relationName, $value);
        }
        
        return $result;
    }
    
    protected function saveRelation($name, $value)
    {
        $relation = $this->{$name}();
        $model = $relation->getModel();
        $foreignKey = $relation->getForeignKeyName();
        
        if (is_a($relation, HasMany::class)) {
            foreach ($value as $v) {
                if (!isset($v['id']) || !($item = $model::find($v['id']))) {
                    $item = new $model;
                }
                $item->{$foreignKey} = $this->id;
                $item->fill($v);
                $item->save();
            }
        }
        
        if (is_a($relation, HasOne::class)) {
            if (!isset($value['id']) || !($item = $model::find($value['id']))) {
                $item = new $model;
            }
            $item->{$foreignKey} = $this->id;
            $item->fill($value);
            $item->save();
        }
    }
}
