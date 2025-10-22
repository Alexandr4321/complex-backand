<?php

namespace App\System\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\System\Models\Relations\BelongsToThrough;

trait HasBelongsToThrough
{
    /**
     * Define a belongs-to-through relationship.
     *
     * @param string $related
     * @param array|string $through
     * @param array $foreignKeyLookup
     * @return BelongsToThrough
     */
    public function belongsToThrough($related, $through, $foreignKeyLookup = [])
    {
        $relatedInstance = $this->newRelatedInstance($related);
        $throughParents = [];
        $foreignKeys = [];
        
        foreach ((array) $through as $model) {
            $foreignKey = null;
            
            if (is_array($model)) {
                $foreignKey = $model[1];
                
                $model = $model[0];
            }
            
            $instance = $this->belongsToThroughParentInstance($model);
            
            if ($foreignKey) {
                $foreignKeys[$instance->getTable()] = $foreignKey;
            }
            
            $throughParents[] = $instance;
        }
        
        foreach ($foreignKeyLookup as $model => $foreignKey) {
            if ($foreignKey) {
                $foreignKeys[(new $model)->getTable()] = $foreignKey;
            }
        }
        
        return $this->newBelongsToThrough($relatedInstance->newQuery(), $this, $throughParents, '', $foreignKeys);
    }
    
    /**
     * Create a through parent instance for a belongs-to-through relationship.
     *
     * @param string $model
     * @return Model
     */
    protected function belongsToThroughParentInstance($model)
    {
        $segments = preg_split('/\s+as\s+/i', $model);
        
        /** @var \Illuminate\Database\Eloquent\Model $instance */
        $instance = new $segments[0];
        
        if (isset($segments[1])) {
            $instance->setTable($instance->getTable().' as '.$segments[1]);
        }
        
        return $instance;
    }
    
    /**
     * Instantiate a new BelongsToThrough relationship.
     *
     * @param Builder $query
     * @param Model $parent
     * @param Model[] $throughParents
     * @param string $prefix
     * @param array $foreignKeyLookup
     * @return BelongsToThrough
     */
    protected function newBelongsToThrough(Builder $query, Model $parent, array $throughParents, $prefix, array $foreignKeyLookup)
    {
        return new BelongsToThrough($query, $parent, $throughParents, $prefix, $foreignKeyLookup);
    }
}
