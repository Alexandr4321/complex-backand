<?php

namespace App\System\Generator\Console\Model;

use App\System\Generator\Console\IsModuleCommand;
use Illuminate\Console\GeneratorCommand;

class SeederMakeCommand extends GeneratorCommand
{
    use IsModuleCommand;
    
    protected $stubName = 'model/seeder.stub';
    
    protected $namespace = '\Seeds';
    
    protected $path = '/database/Seeds';
    
    protected $classType = 'Seeder';
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:seeder';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new seeder class';
    
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Seeder';
    
    /**
     * Build the class with the given name.
     *
     * @param  string $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        $stub = $this->replaceRole($stub);
        
        return $stub;
    }
    
    /**
     * Replace DummyRole in stub.
     *
     * @param $stub
     * @return mixed
     */
    protected function replaceRole($stub)
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
