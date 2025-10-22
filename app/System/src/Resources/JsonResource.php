<?php

namespace App\System\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Arr;
use JsonSerializable;

class JsonResource implements JsonSerializable
{

    protected $model;

    protected $isCollection = false;

    protected $isEmpty = true; // если пришла модель с данными

    protected $params = [];

    protected $maxSize = 1000;

    protected $customData = [];

    public $maxNesting = 0;

    protected $deepField = 'deep';

    protected $childrenField = 'children';

    protected $ignoreFilter = [];

    protected $sortBy = 'id';

    protected $sortDir = 'desc';

    /**
     * JsonResource constructor.
     * @param $model
     * @param null $params
     */
    public function __construct($model = null, $params = null)
    {
        $this->params = $params === null ? request()->query() : $params;

        if (gettype($model) === 'string') {
            $model = $model::query()->select(table($model, '*'));
            $this->isCollection = true;
        } else {
            if (is_a($model, Model::class)) {
                $this->isCollection = false;
            } else {
                if (is_a($model, Collection::class) || is_a($model, \Illuminate\Support\Collection::class)) {
                    $this->isEmpty = false;
                } elseif (!is_null($model)) {
                    $model->addSelect(table($model->getModel(), '*'));
                }

                $this->isCollection = true;
            }
        }

        $this->model = $model;
    }

    /**
     * @param $name
     * @param $value
     */
    public function addData($name, $value)
    {
        $this->customData[$name] = $value;
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->customData = $data;
    }

    /**
     * @param  {any}  $model
     * @param  {array}  $params
     * @return array
     */
    public function fields($model, $params)
    {
        return [];
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [];
    }

    /**
     * Заполняем коллекцию данными.
     *
     * @param  {array}  $collection
     * @param  {array}  $params
     * @param  {string}  $key
     * @return array
     */
    protected function collection($collection, $params, $key)
    {
        $result = [];

        foreach ($collection as $model) {
            $this->addToCollection($result, $model, $key);
        }

        return $result;
    }

    /**
     * Добавляем $item в $result, попутно прогоняя через toArray.
     * $result может быть как обычным массивом так и ассоциативным.
     *
     * @param $result
     * @param $item
     * @param $key
     * @param bool $toArray
     */
    protected function addToCollection(&$result, $item, $key, $toArray = true)
    {
        if ($toArray) {
            if ($key && isset($item->{$key})) {
                $result[$item->{$key}] = $this->toArray($item);
            } else {
                $result[] = $this->toArray($item);
            }
        } else {
            if ($key && isset($item[$key])) {
                $result[$item[$key]] = $item;
            } else {
                $result[] = $item;
            }
        }
    }

    /**
     * @param  {array}  $collection
     * @param  {string}  $childrenName
     * @param  {array}  $params
     * @param  {string}  $key
     * @return array
     */
    protected function tree($collection, $childrenName, $params, $key)
    {
        $collection = $collection->get();
        $result = [];
        $newCollection = [];

        foreach ($collection as $model) {
            $newCollection[$model->id] = $this->toArray($model);
            $newCollection[$model->id][$childrenName] = [];
        }

        foreach ($collection as $model) {
            if ($model->parentId) {
                $this->addToCollection($newCollection[$model->parentId][$childrenName], $newCollection[$model->id], $key, false);
            } else {
                $this->addToCollection($result, $newCollection[$model->id], $key, false);
            }
        }
        return $result;
    }

    /**
     * Используется в функции fields как if
     *
     * @param $conditional
     * @param $a
     * @param null $b
     * @return MissingValue|mixed
     */
    protected function when($conditional, $a, $b = null)
    {
        if ($conditional) {
            return value($a);
        }

        return $b === null ? new MissingValue() : value($b);
    }

    /**
     * Используется для определения отношения в функции fields.
     *
     * @param $resource
     * @param $callback
     * @param $name
     * @return Relation
     */
    protected function relation($resource, $callback = null, $name = null)
    {
        return new Relation($resource, $callback, $name);
    }

    /**
     * Используется в функции fields для дополнения запроса.
     *
     * @param $queryCallback
     * @param $callback
     * @return ResourceQuery
     */
    protected function query($queryCallback, $callback)
    {
        return new ResourceQuery($queryCallback, $callback);
    }

    /**
     * @param string $type Value type
     * @param boolean $name
     * @return ResourceScope
     */
    protected function scope($type, $name = null)
    {
        return new ResourceScope($type, $name);
    }

    /**
     * @param  {any}  $model
     * @param  {array}  $request
     * @return array
     */
    public function toArray($model)
    {
        if (is_null($model)) {
            return [];
        }

        $result = [];
        $fields = array_diff(explode(',', Arr::get($this->params, 'fields', '')), ['']);
        $with = $this->maxNesting < 3 ? Arr::get($this->params, 'with', []) : [];
        $relations = $this->relations();

        if (config('app.env') === 'testing' && $this->maxNesting === 0) {
            $with = array_map(function () {
                return null;
            }, $relations);
        }

        foreach ($this->fields($model, $this->params) as $key => $value) {
            if (is_object($value) && is_a($value, MissingValue::class)) {
                continue;
            }
            if (count($fields) && !in_array($key, $fields)) {
                continue;
            }

            $result[$key] = $value;
        }
        foreach ($relations as $name => $relation) {
            if (is_object($relation) && is_a($relation, Relation::class)) {
                if (!in_array($name, $with) && $model->relationLoaded($name)) {
                    $with[$name] = '';
                }
            }
        }
        foreach ($with as $name => $requestValue) {
            if (($value = Arr::get($relations, $name, false)) !== false) {
                if (is_object($value) && is_a($value, MissingValue::class)) {
                    continue;
                }
                if (is_object($value) && is_a($value, Relation::class)) {
                    $resource = $value->getResource();
                    $callback = $value->getCallback();
                    $relationName = $value->getName($name);
                    $relationValue = $model;

                    if ($resource) {
                        $params = $requestValue ?: [];
                        $params['all'] = true;
                        $relationValue = new $resource($model->{$relationName}, $params);
                        $relationValue->maxNesting = $this->maxNesting + 1;
                    }
                    if ($callback) {
                        $relationValue = $callback($relationValue);
                    }

                    $result[$name] = $relationValue;
                    continue;
                }
                if (is_object($value) && is_a($value, ResourceQuery::class)) {
                    if (!$this->isCollection && $this->isEmpty) {
                        $class = get_class($model);
                        $query = $class::where(table($class, 'id'), $model->id);
                        $value->query($query);
                        $m = $query->first();
                        $result[$name] = $value->call($m);
                    } else {
                        $result[$name] = $value->call($model);
                    }
                    continue;
                }

                $result[$name] = $value;
            }
        }

        return $result;
    }

    public function toCollection()
    {
        $params = $this->initQueryParams($this->params);
        $collection = $this->model;

        if ($this->isEmpty) {
            $this->filterCollection($collection, $params['filter']);
            $this->sortCollection($collection, $params['sort'], $params['tree']);
            if ($this->maxNesting < 3) {
                $this->loadWithCollection($collection, $params['with'], $this->relations());
            }
            if ($params['tree']) {
                $collection = $collection->tree();
            } else {
                $collection = $collection->paginate($params['size']);
            }
        } else {
            $params['all'] = true;
        }

        if ($params['tree']) {
            $result = $this->tree($collection, $this->childrenField, $this->params, $params['byKey']);
        } else {
            $result = $this->collection($collection, $this->params, $params['byKey']);
        }

        $result = $this->getResult($result, $collection, $params);

        return $result;
    }

    private function initQueryParams($params)
    {
        $default = [
            'page' => 1,
            'size' => 100,
            'all' => false,
            'tree' => false,

            'sort' => [],
            'search' => '',
            'filter' => [],
            'byKey' => false,

            'with' => [],
        ];

        $params = array_merge($default, $params);

        $params['all'] = $this->toBoolean($params['all']);
        $params['tree'] = $this->toBoolean($params['tree']);
        $params['byKey'] = $this->toBoolean($params['byKey']);
        $params['page'] = intval($params['page']);
        $params['size'] = intval($params['size']);

        if ($params['byKey']) {
            $params['byKey'] = 'id';
        }
        if (($params['size'] > $this->maxSize) || $params['all']) {
            $params['size'] = $this->maxSize;
        }
        if (gettype($params['sort']) === 'string') {
            $params['sort'] = [$params['sort'],];
        }

        return $params;
    }

    private function toBoolean($param)
    {
        return ($param === null || $param === 'true' || $param === 1 || $param === true || $param === '');
    }

    protected function filterCollection($query, $filters)
    {
        foreach ($this->filters() as $field => $scope) {
            if (!is_a($scope, ResourceScope::class)) {
                $field = $scope;
                $scope = null;
            }
            if (!is_a($requestValue = Arr::get($filters, $field, new MissingValue()), MissingValue::class)) {
                $delimPos = strpos($requestValue, ':') ?: 0;
                $type = substr($requestValue, 0, $delimPos) ?: 'is';
                $value = substr($requestValue, $delimPos ? $delimPos + 1 : 0);
                $values = $this->castValue($query, $field, explode(',', $value), $scope ? $scope->getType() : null);

                if (!is_null($scope)) {
                    $scope->call($query, $values, $type, $field);
                } else {
                    $this->defaultFilter($query, $field, $values, $type);
                }
            }
        }
    }

    public function filters()
    {
        return [];
    }

    /**
     * @param Builder $query
     * @param string $field
     * @param array $values
     * @param string $type is, in, not, search, between
     */
    protected function defaultFilter($query, $field, $values, $type)
    {

        if ($type === 'is') {
            if ($values[0] === 'null') {
                $query->whereNull(table($query->getModel(), $field));
            } else {
                $query->where(table($query->getModel(), $field), $this->castValue($query, $field, $values[0]));
            }
        } elseif ($type === 'in') {
            $query->whereIn(table($query->getModel(), $field), $values);
        } elseif ($type === 'not') {
            $query->whereNotIn(table($query->getModel(), $field), $values);
        } elseif ($type === 'search') {
            $query->where(table($query->getModel(), $field), 'like', '%' . $values[0] . '%');
        } elseif ($type === 'between') {
            $min = Arr::get($values, 0);
            $max = Arr::get($values, 1);
            if (!is_null($min) && !is_null($max)) {
                $query->where(table($query->getModel(), $field), '>', $min);
                $query->where(table($query->getModel(), $field), '<', $max);
            }
        }
    }

    private function sortCollection($collection, $sort, $isTree)
    {
        $table = $isTree ? '' : $collection->getModel()->getTable() . '.';
        if ($sort) {
            foreach ($sort as $s) {
                $sortParams = explode(',', $s);
                $field = Arr::get($sortParams, 0, false);
                $dir = Arr::get($sortParams, 1, 'desc');
                if ($field) {
                    $collection->orderBy($field, $dir === 'desc' ? 'desc' : 'asc');
                }
            }
        } else {
            $collection->orderBy($table . $this->sortBy, $this->sortDir);
        }
    }

    private function loadWithCollection($collection, $withParam, $relations, $parent = '')
    {
        if ($withParam) {
            foreach ($withParam as $paramName => $paramValue) {
                if (($relationValue = Arr::get($relations, $paramName, false)) !== false) {
                    if (is_object($relationValue) && is_a($relationValue, MissingValue::class)) {
                        continue;
                    }
                    if (is_object($relationValue) && is_a($relationValue, Relation::class)) {
                        $relationName = $relationValue->getName($paramName);

                        $collection->with([
                            $parent . $relationName => function ($query) {
                                $query->offset(0)->limit($this->maxSize);
                            },
                        ]);

                        if ($resource = $relationValue->getResource()) {
                            if ($withParam2 = Arr::get($paramValue, 'with')) {
                                $this->loadWithCollection($collection, $withParam2, (new $resource(null))->relations(), $relationName . '.');
                            }
                        }
                        continue;
                    }
                    if (is_object($relationValue) && is_a($relationValue, ResourceQuery::class)) {
                        $relationValue->query($collection);
                    }
                }
            }
        }
    }

    private function getResult($result, $collection, $params)
    {
        if ($params['all'] || $params['tree']) {
            if (!empty($this->customData)) {
                $result = [
                    'data' => $result,
                ];
            }
        } else {
            $pagination = [
                'page' => $params['page'],
                'size' => $params['size'],
                'total' => $collection->total(),
            ];

            $result = [
                'data' => $result,
                'pagination' => $pagination,
            ];
        }

        if (!empty($this->customData)) {
            $result = array_merge($result, $this->customData);
        }

        if (Arr::get($params, 'with.childrenIds') !== null) {
            $table = $this->model->getModel()->getTable();
            $roots = $this->model
                ->select($table . '.' . 'id')
                ->whereNull($table . '.parentId')
                ->get()
                ->map(function ($item) {
                    return $item['id'];
                });
            $result['tree'] = [
                'roots' => $roots,
            ];
        }

        return $result;
    }

    private function castValue($collection, $name, $values, $type = null)
    {
        if (!is_array($values)) {
            $values = [$values,];
        }

        $result = [];
        foreach ($values as $value) {
            $model = $collection->getModel();
            $type = $type ?: Arr::get($model->getCasts(), $name);
            if ($type === 'boolean') {
                if (in_array($value, ['true', '1', '', null])) {
                    $value = true;
                } elseif (in_array($value, ['false', '0'])) {
                    $value = false;
                } else {
                    $value = null;
                }
            }

            if ($type) {
                $result[] = \App\System\Models\Model::cast($type, $value);
            } else {
                $model->setAttribute($name, $value);
                $result[] = $model->getAttributeValue($name);
            }
        }

        return $result;
    }


    /**
     * @return array|null
     */
    public function jsonSerialize()
    {
        $result = null;

        if ($this->model) {
            if ($this->isCollection) {
                $result = $this->toCollection();
            } else {
                $result = $this->toArray($this->model);
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isCollection()
    {
        return $this->isCollection;
    }

}
