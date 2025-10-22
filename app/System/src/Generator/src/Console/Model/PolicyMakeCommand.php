<?php

namespace App\System\Generator\Console\Model;

use App\System\Generator\Console\IsModuleCommand;
use Illuminate\Foundation\Console\PolicyMakeCommand as BasePolicyMakeCommand;

class PolicyMakeCommand extends BasePolicyMakeCommand
{
    use IsModuleCommand;
    
    protected $stubName = 'model/policy.stub';
    
    protected $namespace = '\Policies';
    
    protected $path = '/src/Policies';
    
    protected $classType = 'Policy';
    
    
    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
        $stub = $this->replaceUserNamespace($stub);
        $stub = $this->replaceModels($stub);
        
        return $stub;
    }
    
    /**
     * Replace models in stub.
     *
     * @param $stub
     * @return string
     */
    protected function replaceModels($stub)
    {
        $model = $this->getModelName();
        $namespace = $this->getModuleNamespace() . '\Models\\' . $model;
        $role = strtolower($model);

        $stub = str_replace('NamespacedDummyModel', $namespace , $stub);
        $stub = str_replace('DummyModel', $model, $stub);
        $stub = str_replace('DummyRole', $role, $stub);

        return $stub;
    }
}
