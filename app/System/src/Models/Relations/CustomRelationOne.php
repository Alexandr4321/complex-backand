<?php

namespace App\System\Models;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CustomRelationOne extends CustomRelation
{
    /**
     * Create a new belongs to relationship instance.
     *
     * @param  Builder  $query
     * @param  Model  $parent
     * @param  Closure  $baseConstraints
     * @param  Closure  $eagerConstraints
     * @param  Closure  $eagerMatcher
     * @return void
     */
    public function __construct(Builder $query, Model $parent, $foreignKey, $localKey, $matcherCallback, $resultCallback)
    {
        parent::__construct($query, $parent, $foreignKey, $localKey, $matcherCallback, $resultCallback);
    }
    
    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  Collection  $results
     * @param  string  $relation
     * @return array
     */
    protected function defaultMatch(array $models, Collection $results, $relation)
    {
        $key = last(explode('.', $this->foreignKey));
        $dictionary = $results->keyBy($key);
        
        foreach ($models as $model) {
            if ($value = Arr::get($dictionary, $model->{$this->localKey})) {
                $model->setRelation($relation, $value);
            }
        }
        
        return $models;
    }
}
