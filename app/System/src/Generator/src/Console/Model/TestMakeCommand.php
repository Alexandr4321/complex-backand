<?php

namespace App\System\Generator\Console\Model;

use App\System\Generator\Console\IsModuleCommand;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class TestMakeCommand extends GeneratorCommand
{
    use IsModuleCommand;
    
    protected $stubName = 'model/test.stub';
    
    protected $pivotStubName = 'pivot/test.stub';
    
    protected $namespace = '\Tests\Integration';
    
    protected $path = '/tests/Integration';
    
    protected $classType = 'Test';
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:test';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new test class';
    
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Test';
    
    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->laravel->getNamespace();
    }
    
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
        $stub = $this->replacePivotModels($stub);
        
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
        $namespace = $this->getModuleNamespace() . '\Models\\' . $model;
        $modelRoute = strtolower(strtolower($this->getModelName()));
        
        $stub = str_replace('NamespacedDummyModel', $namespace, $stub);
        $stub = str_replace('DummyModelRoute', $modelRoute, $stub);
        $stub = str_replace('DummyModel', $model, $stub);
        
        return $stub;
    }
    
    /**
     * Replace models in stub.
     *
     * @param $stub
     * @return string
     */
    protected function replacePivotModels($stub)
    {
        $pivotId1 = '';
        $pivotId2 = '';
        $pivotRouteName1 = '';
        $pivotRouteName2 = '';
        $namespacePivotModel1 = '';
        $namespacePivotModel2 = '';
        $pivotModel1 = '';
        $pivotModel2 = '';
        
        $pivots = Arr::get($this->model, 'pivot', []);
        if (count($pivots)) {
            $keys = array_keys($pivots);
            $values = array_values($pivots);
            $pivotModel1 = $this->getNameFromNamespace($keys[0]);
            $pivotModel2 = $this->getNameFromNamespace($keys[1]);
            $pivotId1 = strtolower($pivotModel1) . 'Id';
            $pivotId2 = strtolower($pivotModel2) . 'Id';
            $pivotRouteName1 = Str::ucfirst($values[1]);
            $pivotRouteName2 = Str::ucfirst($values[0]);
            $namespacePivotModel1 = $this->getNamespaceByName($keys[0]);
            $namespacePivotModel2 = $this->getNamespaceByName($keys[1]);
        }
        $model = $this->getModelName();
        $namespace = $this->getModuleNamespace() . '\Models\\' . $model;
        $modelRoute = strtolower($this->model['plural']);
    
        $stub = str_replace('NamespacedDummyModel', $namespace, $stub);
        $stub = str_replace('NamespacedDummyPivotModel1', $namespacePivotModel1, $stub);
        $stub = str_replace('NamespacedDummyPivotModel2', $namespacePivotModel2, $stub);
        $stub = str_replace('DummyPivotModel1', $pivotModel1, $stub);
        $stub = str_replace('DummyPivotModel2', $pivotModel2, $stub);
        $stub = str_replace('DummyModelRoute', $modelRoute, $stub);
        $stub = str_replace('DummyPivotId1', $pivotId1, $stub);
        $stub = str_replace('DummyPivotId2', $pivotId2, $stub);
        $stub = str_replace('DummyRouteName1', $pivotRouteName1, $stub);
        $stub = str_replace('DummyRouteName2', $pivotRouteName2, $stub);
        
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
                $modelRoute = strtolower($this->getModelName());
                $modelAction = Str::ucfirst($relationName);
                $relationModel = $this->getNameFromNamespace($relation[1]);
                $relationModelId = $relation[2];
                
                $actions[] = "
    /** @test */
    public function test_get_$relationName()
    {
        \$model = factory($model::class)->create();
        factory($relationModel::class)->create([
            '$relationModelId' => \$model->id,
        ]);
        factory($relationModel::class)->create([
            '$relationModelId' => \$model->id,
        ]);
        factory($relationModel::class)->create([
            '$relationModelId' => \$model->id,
        ]);
        
        \$route = route('$modelRoute.get$modelAction', ['id' => \$model->id]);
        \$wrongRoute = route('$modelRoute.get$modelAction', ['id' => 0]);
        
        \$response = \$this->getJson(\$route);
        \$response->assertStatus(401);
        
        \$this->authPoor();
        \$response = \$this->getJson(\$route);
        \$response->assertStatus(403);
        
        \$this->authReach();
        \$response = \$this->getJson(\$wrongRoute);
        \$response->assertStatus(404);
        
        \$response = \$this->getJson(\$route);
        \$response->assertStatus(200);
    }";
                
                if ($relation[1] !== $this->getModelName()) {
                    $namespacedModel = trim($relation[1]);
                    if (strpos($namespacedModel, '\\') === false) {
                        $namespacedModel = $this->getModuleNamespace() . '\Models\\' . $namespacedModel;
                    }
    
                    $namespaces[] = "use $namespacedModel;";
                }
            }
        }
        
        if (count($this->model['relations'])) {
            $namespaces = PHP_EOL.implode(PHP_EOL, $namespaces);
            $actions = PHP_EOL.implode(PHP_EOL, $actions);
        } else {
            $namespaces = '';
            $actions = '';
        }
        
        $stub = str_replace('NamespacedDummyRelations', $namespaces , $stub);
        $stub = str_replace('DummyRelations', $actions, $stub);
        
        return $stub;
    }
}
