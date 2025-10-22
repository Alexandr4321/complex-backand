<?php

namespace App\System\Generator\Console\Model;

use App\System\Generator\Console\IsModuleCommand;
use Illuminate\Database\Console\Factories\FactoryMakeCommand as BaseFactoryMakeCommand;
use Illuminate\Support\Arr;

class FactoryMakeCommand extends BaseFactoryMakeCommand
{
    use IsModuleCommand;
    
    protected $stubName = 'model/factory.stub';
    
    protected $namespace = '';
    
    protected $path = '/database/Factories';
    
    protected $classType = 'Factory';
    
    
    /**
     * Build the class with the given name.
     *
     * @param  string $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
        $stub = $this->replaceModel($stub);
        $stub = $this->replaceProperties($stub);
        
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
        
        $stub = str_replace('NamespacedDummyModel', $namespace , $stub);
        $stub = str_replace('DummyModel', $model, $stub);
        
        return $stub;
    }
    
    /**
     * Replace models in stub.
     *
     * @param $stub
     * @return string
     */
    protected function replaceProperties($stub)
    {
        $properties = [];
        $eol = PHP_EOL.'        ';
    
        foreach (Arr::get($this->model, 'pivot', []) as $name => $plural) {
            $name = strtolower($this->getNameFromNamespace($name)).'Id';
            $properties[] = "'$name' => \$faker->numberBetween(1, 10),";
        }
        foreach ($this->model['relations'] as $relationName => $r) {
            if ($r[0] !== 'belongsTo') continue;
        
            $name = strtolower($this->getNameFromNamespace($relationName)).'Id';
            $properties[] = "'$name' => \$faker->numberBetween(1, 1),";
        }
        foreach ($this->model['props'] as $propName => $prop) {
            $properties[] = $this->getProp($prop['rules'], $this->getNameFromNamespace($propName));
        }
    
        $properties = implode($eol, $properties);
        
        $stub = str_replace('DummyProperties', $properties, $stub);
        
        return $stub;
    }
    
    /**
     * Get string row of parameter by rules
     *
     * @param $rules
     * @param $name
     * @param $max
     * @return string
     */
    protected function getProp($rules, $name) {
        $max = $this->getMax($rules);
        $min = $this->getMin($rules);
        $uniq = (in_array('unique', $rules)) ? '->unique()' : '';
        
        if (in_array('email', $rules)) {
            return "'$name' => \$faker$uniq"."->safeEmail,";
        }
        
        foreach ($rules as $rule) {
            if (stripos($rule, 'in:') === 0) {
                $quotify = function($value) { return "'$value',"; };
                $params = explode(',', explode(':', $rule)[1]);
                $params = implode(' ', array_map($quotify, $params));
                return "'$name' => \$faker->randomElement([ $params ]),";
            }
        }
        
        foreach ($rules as $rule) {
            if ($rule === 'string') {
                return "'$name' => \$faker$uniq"."->text($max),";
            }
            if ($rule === 'integer') {
                return "'$name' => \$faker$uniq"."->numberBetween($min, $max),";
            }
            if ($rule === 'boolean') {
                return "'$name' => \$faker$uniq"."->boolean,";
            }
            if ($rule === 'date') {
                return "'$name' => \$faker$uniq"."->iso8601,";
            }
            if ($rule === 'ip') {
                return "'$name' => \$faker$uniq"."->ipv4,";
            }
            if ($rule === 'json') {
                return "'$name' => '{}',";
            }
            if ($rule === 'date_format:H:i') {
                return "'$name' => \$faker$uniq"."->time('H:i'),";
            }
            if ($rule === 'numeric') {
                $m = 2;
                return "'$name' => \$faker$uniq"."->randomFloat($m, $min, $max),";
            }
            if ($pos = stripos($rule, 'digits:') === 0) {
                $length = substr($rule, $pos + 6);
                $mn = 10^($length-1);
                $mx = (10^$length)-1;
                return "'$name' => \$faker$uniq"."->randomFloat($length, $mn, $mx),";
            }
        }
    
        return "'$name' => \$faker$uniq"."->text(255),";
    }
    
    /**
     * Get max value of a parameter by rules
     *
     * @param $rules
     * @return int|string
     */
    protected function getMax($rules) {
        foreach ($rules as $rule) {
            if ($pos = stripos($rule, 'max') !== false) {
                return substr($rule, $pos + 3);
            }
            if (stripos($rule, 'between') !== false) {
                return implode(',', $rule)[1];
            }
        }
        
        return 255;
    }
    
    /**
     * Get min value of a parameter by rules
     *
     * @param $rules
     * @return int|string
     */
    protected function getMin($rules) {
        foreach ($rules as $rule) {
            if ($pos = stripos($rule, 'min') !== false) {
                return substr($rule, $pos + 3);
            }
            if (stripos($rule, 'between') !== false) {
                return implode(',', $rule)[0];
            }
        }
        
        return 0;
    }
}
