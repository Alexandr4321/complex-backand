<?php

namespace App\System\Generator\Console;

use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\InputOption;

trait IsModuleCommand
{
    use UseConfig;
  
    protected $models = [];
    protected $model = [];
  
    /**
    * Execute the console command.
    *
    * @return void
    * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
    */
    public function handle()
    {
        $this->initModel();
        
        parent::handle();
    }
    
    public function initModel()
    {
        $this->models = $this->getModels($this->getModuleName());
        $this->model = Arr::get($this->models, $this->getModelName(), []);
    }
    
    /**
    * Return module name.
    *
    * @return string
    */
    protected function getModuleName()
    {
        $name = trim($this->option('module'));
        return $name ?: 'Sample';
    }
    
    /**
    * Return module name.
    *
    * @return string
    */
    protected function getModuleNamespace()
    {
        return trim($this->rootNamespace(), '\\') . '\\' . $this->getModuleName();
    }
    
    /**
     * Return module name.
     *
     * @return string
     */
    protected function getModelName()
    {
        return $this->getNameInput();
    }
    
    /**
     * Return module name.
     *
     * @return string
     */
    protected function getNameFromNamespace($namespace)
    {
        $arr = explode('\\', $namespace);
        return $arr[count($arr) - 1];
    }
    
    /**
     * @param $name
     * @return mixed
     */
    protected function getNamespaceByName($name)
    {
        if ($name == $this->getNameFromNamespace($name)) {
            $namespace = $this->getModuleNamespace().'\Models\\'.$name;
        } else {
            $namespace = trim($name, '\\');
        }
        
        return $namespace;
    }
    
    /**
    * Return path of the stub.
    *
    * @return string
    */
    protected function getStubPath()
    {
        return base_path('app/System/Generator/resources/stubs');
    }
    
    /**
     * @return boolean
     */
    protected function isPivot()
    {
        return (boolean) Arr::get($this->model, 'pivot', false);
    }
    
    /**
    * Get the stub file for the generator.
    *
    * @return string
    */
    protected function getStub()
    {
        $stubName = $this->stubName;
        
        if ($this->isPivot()) {
            if (isset($this->pivotStubName)) {
                $stubName = $this->pivotStubName;
            }
        }
        
        $stub = $this->getStubPath() . '/' . $stubName;
        
        if (file_exists($stub)) {
            return $stub;
        } else {
            echo "Stub $stub was not found!";
            return '';
        }
    }
    
    /**
    * Return name of the file.
    *
    * @return string
    */
    protected function getFileName()
    {
        $classType = isset($this->classType) ? $this->classType : '';
        $fileName = str_replace('\\', '/', $this->getModelName()) . $classType . '.php';
        
        return $fileName;
    }
    
    /**
    * Get the destination class path.
    *
    * @param  string  $name
    * @return string
    */
    protected function getPath($name)
    {
        $modulePath = str_replace('\\', '/', $this->getModuleName());
        
        return $this->laravel['path'] . '\\' . $modulePath . $this->path . '/' . $this->getFileName();
    }
    
    /**
    * Replace the class name for the given stub.
    *
    * @param  string  $stub
    * @param  string  $name
    * @return string
    */
    protected function replaceClass($stub, $name)
    {
        $classType = isset($this->classType) ? $this->classType : '';
        $class = str_replace($this->getNamespace($name).'\\', '', $name);
        
        return str_replace('DummyClass', $class.$classType, $stub);
    }
    
    /**
    * Get the console command options.
    *
    * @return array
    */
    protected function getOptions()
    {
        $options = parent::getOptions();
    
        $options[] = ['module', null, InputOption::VALUE_OPTIONAL, 'Set module name.'];
        
        return $options;
    }
    
    /**
    * Get the default namespace for the class.
    *
    * @param  string $rootNamespace
    * @return string
    */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . $this->getModuleName() . $this->namespace;
    }
}
