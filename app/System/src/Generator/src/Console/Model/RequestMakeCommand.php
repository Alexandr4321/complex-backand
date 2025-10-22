<?php

namespace App\System\Generator\Console\Model;

use App\System\Generator\Console\IsModuleCommand;
use Illuminate\Foundation\Console\RequestMakeCommand as BaseRequestMakeCommand;

class RequestMakeCommand extends BaseRequestMakeCommand
{
    use IsModuleCommand;
    
    protected $stubName = 'model/request.stub';
    
    protected $namespace = '\Requests';
    
    protected $path = '/src/Requests';
    
    protected $classType = 'Request';
    
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
        $model = '';
        $comments = [];
        $rules = [];
    
        foreach ($this->model['props'] as $name => $prop) {
            
            $modelRules = isset($prop['rules']) ? $prop['rules'] : [];
            foreach ($modelRules as $key => $rule) {
                if ($rule === 'unique') {
                    $model = $this->getModuleNamespace() . '\Models\\' . $this->getModelName();
                    $model = 'use ' . $model . ';';
                    $modelRules[$key] = "'unique:'.(new ".$this->getModelName().")->getTable().',$name'.\$this->uniqueExceptId,";
                } else {
                    $modelRules[$key] = "'$rule',";
                }
            }
            $modelRules = implode(' ', $modelRules);
            
            $rules[] = "'$name' => [ $modelRules ],";
            
            $title = isset($prop['title']) ? $prop['title'] : '';
            $comments[] = "@$name $title";
        }
    
        $rules = implode(PHP_EOL.'            ', $rules);
        $comments = implode(PHP_EOL.'     * ', $comments);
        
        $stub = str_replace('DummyComments', $comments , $stub);
        $stub = str_replace('DummyRules', $rules, $stub);
        $stub = str_replace('NamespacedDummyModel', $model, $stub);
        
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
}
