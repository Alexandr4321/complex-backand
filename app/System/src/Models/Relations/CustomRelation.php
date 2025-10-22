<?php

namespace App\System\Models;

use Closure;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CustomRelation extends Relation
{
    /**
     * The Eloquent query builder instance.
     *
     * @var Builder
     */
    protected $query;
    
    /**
     * The parent model instance.
     *
     * @var Model
     */
    protected $parent;
    
    /**
     * The related model instance.
     *
     * @var Model
     */
    protected $related;
    
    protected $foreignKey;
    protected $localKey;
    protected $matcherCallback;
    protected $resultCallback;
    
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
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
        $this->matcherCallback = $matcherCallback;
        $this->resultCallback = $resultCallback;
        
        parent::__construct($query, $parent);
    }
    
    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query->where($this->foreignKey, '=', $this->parent->{$this->localKey});
        }
    }
    
    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        $whereIn = $this->whereInMethod($this->parent, $this->localKey);
        $this->query->{$whereIn}($this->foreignKey, $this->getEagerModelKeys($models));
    }
    
    /**
     * Gather the keys from an array of related models.
     *
     * @param  array  $models
     * @return array
     */
    protected function getEagerModelKeys(array $models)
    {
        $keys = [];
        
        // First we need to gather all of the keys from the parent models so we know what
        // to query for via the eager loading query. We will add them to an array then
        // execute a "where in" statement to gather up all of those related records.
        foreach ($models as $model) {
            if (!is_null($value = $model->{$this->localKey})) {
                $keys[] = $value;
            }
        }
        
        sort($keys);
        
        return array_values(array_unique($keys));
    }
    
    /**
     * Initialize the relation on a set of models.
     *
     * @param  array   $models
     * @param  string  $relation
     * @return array
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }
        
        return $models;
    }
    
    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        if (is_callable($this->matcherCallback)) {
            return ($this->matcherCallback)($models, $results, $relation, $this);
        } else {
            return $this->defaultMatch($models, $results, $relation);
        }
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
        $dictionary = [];
        foreach ($results as $result) {
            $key = last(explode('.', $this->foreignKey));
            $dictionary[$result->getAttribute($key)][] = $result;
        }
    
        foreach ($models as $model) {
            if ($value = Arr::get($dictionary, $model->{$this->localKey})) {
                $model->setRelation($relation, $model->newCollection($value));
            }
        }
        
        return $models;
    }
    
    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->get();
    }
    
    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return Collection
     */
    public function get($columns = ['*'])
    {
        // First we'll add the proper select columns onto the query so it is run with
        // the proper columns. Then, we will get the results and hydrate out pivot
        // models with the result of those columns as a separate model relation.
        $columns = $this->query->getQuery()->columns ? [] : $columns;
        
        if ($columns == ['*']) {
            $columns = [$this->related->getTable().'.*'];
        }
        
        $builder = $this->query->applyScopes();
        
        $models = $builder->addSelect($columns)->getModels();
        
        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded. This will solve the
        // n + 1 query problem for the developer and also increase performance.
        if (count($models) > 0) {
            $models = $builder->eagerLoadRelations($models);
        }
        
        return $this->getResult($models);
    }
    
    /**
     * @param  array  $models
     * @return Collection
     */
    protected function getResult(array $models)
    {
        if (is_callable($this->resultCallback)) {
            $models = ($this->resultCallback)($models);
        }
        
        return $this->related->newCollection($models);
    }
}
