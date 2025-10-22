<?php

namespace App\System\Generator\Console\Model;

use App\System\Generator\Console\IsModuleCommand;
use Illuminate\Foundation\Console\ResourceMakeCommand as BaseResourceMakeCommand;
use Illuminate\Support\Arr;

class ResourceMakeCommand extends BaseResourceMakeCommand
{
    use IsModuleCommand;
    
    protected $stubName = 'model/resource.stub';
    
    protected $namespace = '\Resources';
    
    protected $path = '/src/Resources';
    
    protected $classType = 'Resource';
    
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
        $stub = $this->replaceDescription($stub);
        
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
        $resources = [];
        $comments = ["@id  {int}",];
        $properties = ["'id' => \$model->id,",];
        
        foreach (Arr::get($this->model, 'pivot', []) as $name => $plural) {
            $relationKey = strtolower($this->getNameFromNamespace($name)).'Id';
            $properties[] = "'$relationKey' => \$model->$relationKey,";
            $type = $this->getPropType(['integer',]);
            $comments[] = "@$relationKey  {".$type."}";
        }
        foreach ($this->model['relations'] as $name => $relation) {
            if ($relation[0] === 'belongsTo') {
                $rn = $name.'Id';
                $properties[] = "'{$rn}' => \$model->{$rn},";
                $type = $this->getPropType(['integer',]);
                $comments[] = "@{$rn}  {".$type."}";
            }
        }
        foreach ($this->model['props'] as $name => $prop) {
            $properties[] = "'$name' => \$model->$name,";
            $rules = isset($prop['rules']) ? $prop['rules'] : [];
            $title = isset($prop['title']) ? $prop['title'] : '';
            $type = $this->getPropType($rules);
            $comments[] = "@$name  {".$type."}  $title";
        }
        
        $properties = implode(PHP_EOL.'            ', $properties);
        $comments = implode(PHP_EOL.'     * ', $comments);
        $resources = implode(PHP_EOL, $resources);
        
        $stub = str_replace('DummyComments', $comments, $stub);
        $stub = str_replace('DummyProperties', $properties, $stub);
        $stub = str_replace('NamespacedDummyResources', $resources, $stub);
        
        return $stub;
    }
    
    /**
     * Replace models in stub.
     *
     * @param $stub
     * @return string
     */
    protected function replaceDescription($stub)
    {
        $desc = '';
        
        $stub = str_replace('DummyDescription', $desc, $stub);
        
        return $stub;
    }
    
    /**
     * Get type by rules
     *
     * @param $rules
     * @return string
     */
    protected function getPropType($rules)
    {
        foreach ($rules as $rule) {
            if ($rule === 'string') {
                return 'string';
            }
            if ($rule === 'integer' || $rule === 'numeric' || stripos($rule, 'digits:') === 0) {
                return 'number';
            }
            if ($rule === 'boolean') {
                return 'boolean';
            }
            if ($rule === 'date') {
                return 'timestamp';
            }
            if ($rule === 'ip') {
                return 'ipAddress';
            }
            if ($rule === 'json') {
                return 'json';
            }
            if ($rule === 'date_format:H:i') {
                return 'time';
            }
        }
        
        return 'string';
    }
}
