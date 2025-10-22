<?php

namespace App\System\Generator\Console\Model;

use App\System\Generator\Console\IsModuleCommand;
use Illuminate\Foundation\Console\ModelMakeCommand as BaseModelMakeCommand;
use Illuminate\Support\Arr;

class ModelMakeCommand extends BaseModelMakeCommand
{
    use IsModuleCommand;
    
    protected $stubName = 'model/model.stub';
    
    protected $namespace = '\Models';
    
    protected $path = '/src/Models';
    
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
        $stub = $this->replaceRelations($stub);
        $stub = $this->replacePivotRelations($stub);
        $stub = $this->replaceTableName($stub);
        $stub = $this->replaceProps($stub);
        
        return $stub;
    }
    
    protected function replaceTableName($stub)
    {
        $tableName = strtolower($this->getModuleName()).'__'.strtolower($this->model['plural']);
        
        $stub = str_replace('DummyTableName', $tableName, $stub);
        
        return $stub;
    }
    
    protected function replaceProps($stub)
    {
        $guarded = [];
        $hidden = [];
        $attributes = [];
        
        foreach ($this->model['props'] as $name => $prop) {
            if (isset($prop['hidden'])) {
                $hidden[] = "'$name', ";
            }
            if (isset($prop['guarded'])) {
                $guarded[] = "'$name', ";
            }
            if (isset($prop['default'])) {
                $def = $prop['default'];
                $attributes[] = "    '$name' => ".(is_string($def) ? "'$def'" : var_export($def,
                        true)).','.PHP_EOL.'    ';
            }
        }
        
        $guarded = $guarded ? '[ '.implode('', $guarded).']' : '[]';
        $hidden = $hidden ? '[ '.implode('', $hidden).']' : '[]';
        $attributes = $attributes ? '['.PHP_EOL.'    '.implode('', $attributes).']' : '[]';
        
        $stub = str_replace('DummyGuarded', $guarded, $stub);
        $stub = str_replace('DummyHidden', $hidden, $stub);
        $stub = str_replace('DummyAttributes', $attributes, $stub);
        
        return $stub;
    }
    
    protected function replaceRelations($stub)
    {
        $modelRelations = Arr::get($this->model, 'relations', []);
        
        $relations = [];
        $glue = PHP_EOL.PHP_EOL.'    ';
        
        foreach ($modelRelations as $name => $relation) {
            $type = strtolower($relation[0]);
            if ($type === 'hasone') {
                $relations[] = $this->getHasOneString($name, $relation);
            } elseif ($type === 'hasmany') {
                $relations[] = $this->getHasManyString($name, $relation);
            } elseif ($type === 'belongsto') {
                $relations[] = $this->getBelongsToString($name, $relation);
            } elseif ($type === 'belongstomany') {
                $relations[] = $this->getBelongsToManyString($name, $relation);
            }
        }
        
        if (count($modelRelations)) {
            $stub = str_replace('DummyRelations', PHP_EOL.$glue.implode($glue, $relations), $stub);
        } else {
            $stub = str_replace('DummyRelations', '', $stub);
        }
        
        return $stub;
    }
    
    protected function replacePivotRelations($stub)
    {
        $modelRelations = Arr::get($this->model, 'pivot', []);
        
        $relations = [];
        $glue = PHP_EOL.PHP_EOL.'    ';
        
        foreach ($modelRelations as $name => $plural) {
            $relationName = strtolower($this->getNameFromNamespace($name));
            $relations[] = $this->getBelongsToString($relationName, ['belongsTo', $name, 'id', $relationName.'Id',]);
        }
        
        if (count($modelRelations)) {
            $stub = str_replace('DummyPivotRelations', PHP_EOL.$glue.implode($glue, $relations), $stub);
        } else {
            $stub = str_replace('DummyPivotRelations', '', $stub);
        }
    
        return $stub;
    }
    
    /**
     * @param  string  $name
     * @param  array  $relation  [ relationType, Model, foreign, local ]
     * @return string
     */
    private function getHasOneString($name, $relation)
    {
        $foreignKey = isset($relation[2]) ? ", '$relation[2]'" : '';
        $localKey = isset($relation[3]) ? ", '$relation[3]'" : '';
        
        $string = 'public function '.$name.'()
    {
        return $this->hasOne('.$relation[1].'::class'.$foreignKey.$localKey.');
    }';
        
        return $string;
    }
    
    /**
     * @param  string  $name
     * @param  array  $relation  [ relationType, Model, foreign, local ]
     * @return string
     */
    private function getHasManyString($name, $relation)
    {
        $foreignKey = isset($relation[2]) ? ", '$relation[2]'" : '';
        $localKey = isset($relation[3]) ? ", '$relation[3]'" : '';
        
        $string = 'public function '.$name.'()
    {
        return $this->hasMany('.$relation[1].'::class'.$foreignKey.$localKey.');
    }';
        
        return $string;
    }
    
    /**
     * @param  string  $name
     * @param  array  $relation  [ relationType, Model, foreign, local ]
     * @return string
     */
    private function getBelongsToString($name, $relation)
    {
        $foreignKey = isset($relation[2]) ? ", '$relation[2]'" : '';
        $localKey = isset($relation[3]) ? ", '$relation[3]'" : '';
        
        $string = 'public function '.$name.'()
    {
        return $this->belongsTo('.$relation[1].'::class'.$foreignKey.$localKey.');
    }';
        
        return $string;
    }
    
    
    /**
     * @param  string  $name
     * @param  array  $relation  [ relationType, Model ]
     * @return string
     */
    private function getBelongsToManyString($name, $relation)
    {
        $model = '';
        $table = ", $relation[1]::class";
        $foreignKey = '';
        $relatedKey = '';
        
        $keys = $this->models[$relation[1]]['pivot'];
        foreach ($keys as $modelName => $plural) {
            $key = strtolower($this->getNameFromNamespace($modelName)).'Id';
            if ($modelName === $this->getModelName()) {
                $relatedKey = ", '$key'";
            } else {
                $foreignKey = ", '$key'";
                $model = $modelName.'::class';
            }
        }
        
        
        $string = 'public function '.$name.'()
    {
        return $this->belongsToMany('.$model.$table.$foreignKey.$relatedKey.');
    }';
        
        return $string;
    }
}
