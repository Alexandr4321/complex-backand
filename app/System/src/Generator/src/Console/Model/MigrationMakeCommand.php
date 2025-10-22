<?php

namespace App\System\Generator\Console\Model;

use App\System\Generator\Console\IsModuleCommand;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;

class MigrationMakeCommand extends GeneratorCommand
{
    use IsModuleCommand;
    
    protected $stubName = 'model/migration.stub';
    
    protected $namespace = '';
    
    protected $path = '/database/Migrations';
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:migration';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration';
    
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Migration';
    
    /**
     * Return name of the file.
     *
     * @return string
     */
    protected function getFileName()
    {
        $fileName = strtolower($this->model['plural']);
        $index = $this->model['index'];
        
        for ($i = 6 - strlen(strval($index)); $i > 0; $i--) {
            $index = '0'.$index;
        }
        
        return '2000_00_99_'.$index.'_create_'.$fileName.'_table.php';
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
        $stub = $this->replaceTableProperties($stub);
        $stub = $this->replaceTable($stub);
        $stub = $this->replaceRelations($stub);
        
        return $stub;
    }
    
    protected function replaceRelations($stub)
    {
        $properties = [];
        $indexes = [];
        $eol = PHP_EOL.'            ';
        
        foreach ($this->model['relations'] as $name => $values) {
            $type = strtolower($values[0]);
            $class = $values[1];
            
            if ($type !== 'belongsto') {
                continue;
            }
            
            if (!class_exists($class)) {
                $class = $this->getModuleNamespace().'\Models\\'.$class;
            }
            if (class_exists($class)) {
                $table = (new $class())->getTable();
            } else {
                $table = strtolower($this->getModuleName()).'__'.strtolower($this->models[$values[1]]['plural']);
            }
    
            $foreign = strtolower($this->getNameFromNamespace($name)).'Id';
            
            $properties[] = '$'."table->integer('$foreign')->unsigned()->nullable();";
            $indexes[] = '$'."table->foreign('$foreign')->references('id')->on('$table');";
        }
        
        $pivots = Arr::get($this->model, 'pivot', []);
        if ($pivots) {
            $keys = array_keys($pivots);
            $key1 = $this->getNameFromNamespace(strtolower($keys[0]).'Id');
            $key2 = $this->getNameFromNamespace(strtolower($keys[1]).'Id');
            $indexes[] = '$'."table->index(['{$key1}', '{$key2}']);";
        }
        foreach ($pivots as $name => $plural) {
            $values = [ '', $name, strtolower($name).'Id', 'id'];
            $class = $values[1];
            
            if (!class_exists($class)) {
                $class = $this->getModuleNamespace().'\Models\\'.$class;
            }
            if (class_exists($class)) {
                $table = (new $class())->getTable();
            } else {
                $table = strtolower($this->getModuleName()).'__'.strtolower($this->models[$name]['plural']);
            }
    
            $foreign = strtolower($this->getNameFromNamespace($name)).'Id';
            
            $properties[] = '$'."table->integer('$foreign')->unsigned()->nullable();";
            $indexes[] = '$'."table->foreign('$foreign')->references('id')->on('$table');";
        }
        
        $properties = implode($eol, $properties);
        $indexes = implode($eol, $indexes);
        
        if ($properties) {
            $properties = $properties.$eol;
        }
        if ($indexes) {
            $indexes = $eol.$eol.$indexes;
        }
        
        $stub = str_replace('DummyRelationProperties', $properties.$eol, $stub);
        $stub = str_replace('DummyRelationIndexes', $indexes, $stub);
        
        return $stub;
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
        $class = $this->model['plural'];
        
        return str_replace('DummyClass', 'Create'.$class.'Table', $stub);
    }
    
    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function replaceTable($stub)
    {
        $class = strtolower($this->model['plural']);
        $module = ltrim(str_replace('\\', '_', strtolower($this->getModuleName())), '_');
        
        return str_replace('DummyTable', $module.'__'.$class, $stub);
    }
    
    /**
     * Replace the config name for the given stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function replaceTableProperties($stub)
    {
        $properties = [];
        $model = $this->model;
        
        foreach ($model['props'] as $propName => $prop) {
            $properties[] = $this->getProp($prop['rules'], $propName, $this->getMax($prop['rules']),
                $this->getRequired($prop['rules']));
        }
        
        $properties[] = "$"."table->timestamp('createdAt');";
        $properties[] = "$"."table->timestamp('deletedAt')->nullable();";
        
        $properties = implode(PHP_EOL.'            ', $properties);
        
        return str_replace('DummyTableProperties', $properties, $stub);
    }
    
    /**
     * Get string row of parameter by rules
     *
     * @param  array  $rules
     * @param  string  $name
     * @param  int  $max
     * @param  bool  $required
     * @return string
     */
    protected function getProp($rules, $name, $max, $required)
    {
        $req = !$required ? '->nullable()' : '';
        
        if (in_array('email', $rules)) {
            return "$"."table->string('$name', 255)$req;";
        }
        foreach ($rules as $rule) {
            if (stripos($rule, 'in:') === 0) {
                $quotify = function ($value) {
                    return "'$value',";
                };
                $params = explode(',', explode(':', $rule)[1]);
                $params = implode(' ', array_map($quotify, $params));
                return "$"."table->enum('$name', [ $params ])$req;";
            }
        }
        
        foreach ($rules as $rule) {
            if ($rule === 'string') {
                $type = $max <= 65535 ? 'string' : 'mediumText';
                return "$"."table->$type('$name', $max)$req;";
            }
            if ($rule === 'integer') {
                $type = $max <= 32767 ? 'smallInteger' : 'integer';
                return "$"."table->$type('$name')$req;";
            }
            if ($rule === 'boolean') {
                return "$"."table->boolean('$name')$req;";
            }
            if ($rule === 'date') {
                return "$"."table->timestamp('$name')$req;";
            }
            if ($rule === 'ip') {
                return "$"."table->ipAddress('$name')$req;";
            }
            if ($rule === 'json') {
                return "$"."table->json('$name')$req;";
            }
            if ($rule === 'date_format:H:i') {
                return "$"."table->time('$name')$req;";
            }
            if ($rule === 'numeric') {
                $m = strlen(strval($max));
                return "$"."table->decimal('$name', 2, $m)$req;";
            }
            if ($pos = stripos($rule, 'digits:') === 0) {
                $length = substr($rule, $pos + 6);
                return "$"."table->decimal('$name', $length, 0)$req;";
            }
        }
        
        return "$"."table->string('$name', 255)$req;";
    }
    
    /**
     * Get max value of a parameter by rules
     *
     * @param $rules
     * @return int
     */
    protected function getMax($rules)
    {
        foreach ($rules as $rule) {
            if ($pos = stripos($rule, 'max') !== false) {
                return substr($rule, $pos + 3);
            }
            if (stripos($rule, 'between') !== false) {
                return explode(',', $rule)[1];
            }
        }
        
        return 255;
    }
    
    /**
     * Get value if prop is required
     *
     * @param $rules
     * @return boolean
     */
    protected function getRequired($rules)
    {
        foreach ($rules as $rule) {
            if ($rule === 'required') {
                return true;
            }
        }
        
        return false;
    }
}
