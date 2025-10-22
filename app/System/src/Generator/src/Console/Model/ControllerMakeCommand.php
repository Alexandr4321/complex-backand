<?php

namespace App\System\Generator\Console\Model;

use App\System\Generator\Console\IsModuleCommand;
use Illuminate\Routing\Console\ControllerMakeCommand as BaseControllerMakeCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ControllerMakeCommand extends BaseControllerMakeCommand
{
    use IsModuleCommand;
    
    protected $stubName = 'model/controller.stub';
    
    protected $pivotStubName = 'pivot/controller.stub';
    
    protected $namespace = '\Controllers\Api';
    
    protected $path = '/src/Controllers/Api';
    
    protected $classType = 'Controller';
    
    
    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        $stub = $this->replaceModel($stub);
        $stub = $this->replaceRelations($stub);
        $stub = $this->replacePivotRelations($stub);
        
        return $stub;
    }
    
    /**
     * Replace models in stub.
     *
     * @param $stub
     * @return string
     */
    protected function replaceModel($stub)
    {
        $model = $this->getModelName();
        $resource = $model . 'Resource';
        $request = $this->getModelName() . 'Request';
        $namespace = $this->getModuleNamespace() . '\Models\\' . $model;
        $namespacedResource = $this->getModuleNamespace() . '\Resources\\' . $model . 'Resource';
        $namespacedRequest = $this->getModuleNamespace() . '\Requests\\' . $request;
        
        $role = strtolower($model);
        $group = $this->getModuleName() . ' / '. $model;
        
        
        $stub = str_replace('NamespacedDummyModel', $namespace , $stub);
        $stub = str_replace('NamespacedDummyResource', $namespacedResource, $stub);
        $stub = str_replace('NamespacedDummyRequest', $namespacedRequest , $stub);
        $stub = str_replace('DummyModel', $model, $stub);
        $stub = str_replace('DummyResource', $resource, $stub);
        $stub = str_replace('DummyRequest', $request, $stub);
        
        $stub = str_replace('DummyRole', $role, $stub);
        $stub = str_replace('DummyGroup', $group, $stub);
        
        return $stub;
    }
    
    /**
     * Replace models in stub.
     *
     * @param $stub
     * @return string
     */
    protected function replacePivotRelations($stub)
    {
        if (Arr::get($this->model, 'pivot', false)) {
            $models = [];
            $resources = [];
            $pluralU = [];
            $plural = [];
            $id = [];
            $namespacedModels = [];
            $namespacedResources = [];
    
            foreach ($this->model['pivot'] as $name => $pluralName) {
                $n = $this->getNameFromNamespace($name);
                $pluralU[] = Str::ucfirst($pluralName);
                $plural[] = $pluralName;
                $id[] = strtolower($n).'Id';
                $models[] = $n;
                $resources[] = $n.'Resource';
                if ($name === $n) {
                    $namespacedModels[] = $this->getModuleNamespace().'\Models\\'.$n;
                    $namespacedResources[] = $this->getModuleNamespace().'\Resources\\'.$n.'Resource';
                } else {
                    $namespacedModel = trim($name, '\\');
                    $namespacedModels[] = $namespacedModel;
                    $namespacedResources[] = str_replace('Model', 'Resource', $namespacedModel).'Resource';
                }
            }
    
            $stub = str_replace('NamespacedDummyPivotRelation1', $namespacedModels[0], $stub);
            $stub = str_replace('NamespacedDummyPivotRelation2', $namespacedModels[1], $stub);
            $stub = str_replace('NamespacedDummyPivotRelationResource1', $namespacedResources[0], $stub);
            $stub = str_replace('NamespacedDummyPivotRelationResource2', $namespacedResources[1], $stub);
            $stub = str_replace('DummyPivotRelation1', $models[0], $stub);
            $stub = str_replace('DummyPivotRelation2', $models[1], $stub);
            $stub = str_replace('DummyPivotRelationResource1', $resources[0], $stub);
            $stub = str_replace('DummyPivotRelationResource2', $resources[1], $stub);
            $stub = str_replace('DummyPivotRelationPluralU1', $pluralU[0], $stub);
            $stub = str_replace('DummyPivotRelationPluralU2', $pluralU[1], $stub);
            $stub = str_replace('DummyPivotRelationPlural1', $plural[0], $stub);
            $stub = str_replace('DummyPivotRelationPlural2', $plural[1], $stub);
            $stub = str_replace('DummyPivotRelationId1', $id[0], $stub);
            $stub = str_replace('DummyPivotRelationId2', $id[1], $stub);
        }
        return $stub;
    }
    
    /**
     * Replace relations in stub.
     *
     * @param $stub
     * @return string
     */
    protected function replaceRelations($stub)
    {
        $actions = [];
        $namespaces = [];
        
        foreach ($this->model['relations'] as $relationName => $relation) {
            if ($relation[0] === 'hasMany') {
                $model = $this->getModelName();
                $relationUName = ucfirst($relationName);
                $relationModel = $this->getNameFromNamespace($relation[1]);
                
                $actions[] = "
    /**
     * @title Get $relationName by $model
     * @description Get $relationName by $model
     *
     * @param  CollectionRequest  \$request
     * @param  $model  \$model
     * @return \App\System\Responses\JsonResponse
     * @throws AuthorizationException
     */
    public function get$relationUName(CollectionRequest \$request, $model \$model)
    {
        \$this->authorize('read', $relationModel::class);
        
        return \$this->response(new $relationModel"."Resource(\$model->$relationName()));
    }";
    
                if ($relation[1] !== $this->getModelName()) {
                    $namespacedModel = trim($relation[1]);
                    if (strpos($namespacedModel, '\\') === false) {
                        $namespacedModel = $this->getModuleNamespace().'\Models\\'.$namespacedModel;
                    }
                    $namespacedResource = str_replace('Models', 'Resources', $namespacedModel).'Resource';
    
                    $namespaces[] = "use $namespacedModel;";
                    $namespaces[] = "use $namespacedResource;";
                }
            }
        }
    
        $namespaces = PHP_EOL.implode(PHP_EOL, $namespaces);
        $actions = PHP_EOL.implode(PHP_EOL, $actions);
        
        $stub = str_replace('NamespacedDummyRelations', $namespaces , $stub);
        $stub = str_replace('DummyRelations', $actions, $stub);
        
        return $stub;
    }
}
