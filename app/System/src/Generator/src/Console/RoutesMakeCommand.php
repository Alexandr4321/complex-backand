<?php

namespace App\System\Generator\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RoutesMakeCommand extends GeneratorCommand
{
    use IsModuleCommand;
    
    protected $stubName = 'routes.stub';
    
    protected $namespace = '';
    
    protected $path = '/routes';
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:routes';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create routes for models';
    
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Routes';
    
    /**
     * Return name of the file.
     *
     * @return string
     */
    protected function getFileName()
    {
        return 'api.php';
    }
    
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }
    
    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return '';
    }
    
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
        $stub = $this->replaceRoutes($stub);
        
        return $stub;
    }
    
    /**
     * Replace routes in stub.
     *
     * @param $stub
     * @return mixed
     */
    protected function replaceRoutes($stub)
    {
        $routes = [];
        $eol = PHP_EOL.'        ';
        
        foreach ($this->models as $name => $model) {
            $lName = strtolower($name);
            $lNameP = strtolower($model['plural']);
            
            $relationRoutes = [];
            foreach ($model['relations'] as $lRelationName => $relation) {
                if ($relation[0] === 'hasMany') {
                    $relationName = ucfirst($lRelationName);
    
                    $relationRoutes[] = "
                Route::get('{".$lName."}/$lRelationName', '$name"."Controller@get$relationName')->name('get$relationName');";
                }
            }
            $relationRoutes = implode('', $relationRoutes);
            
            $pivots = Arr::get($model, 'pivot', []);
            if (count($pivots)) {
                $keys = array_keys($pivots);
                $plurals = array_values($pivots);
                $id1 = strtolower($this->getNameFromNamespace($keys[0]));
                $id2 = strtolower($this->getNameFromNamespace($keys[1]));
                $plural1 = $plurals[0];
                $plural2 = $plurals[1];
                $pluralU1 = Str::ucfirst($plurals[0]);
                $pluralU2 = Str::ucfirst($plurals[1]);
                $routes[] = "
        // $lNameP
        Route::group(['as' => '$lName.'], function () {
            Route::group(['middleware' => 'auth:api'], function() {
                Route::group(['prefix' => '$lNameP'], function () {
                    Route::get('{{$lName}}', '{$name}Controller@get')->name('get');
                    Route::get('', '{$name}Controller@getList')->name('getList');
                });
                Route::get('$plural2/{{$id2}}/$plural1', '{$name}Controller@getList')->name('get$pluralU1');
                Route::get('$plural1/{{$id1}}/$plural2', '{$name}Controller@getList')->name('get$pluralU2');$relationRoutes
                Route::post('$plural1/{{$id1}Id}/$plural2/{{$id2}Id}', '{$name}Controller@post')->name('post');
                Route::put('$plural1/{{$id1}Id}/$plural2/{{$id2}Id}', '{$name}Controller@put')->name('put');
                Route::delete('$plural1/{{$id1}Id}/$plural2/{{$id2}Id}', '{$name}Controller@delete')->name('delete');
            });
        });";
            } else {
    
                $routes[] = "
        // $lNameP
        Route::group(['prefix' => '$lNameP', 'as' => '$lName.'], function () {
            Route::group(['middleware' => 'auth:api'], function() {
                Route::get('{".$lName."}', '".$name."Controller@get')->name('get');
                Route::get('', '".$name."Controller@getList')->name('getList');$relationRoutes
                Route::post('', '".$name."Controller@post')->name('post');
                Route::put('{".$lName."}', '".$name."Controller@put')->name('put');
                Route::delete('{".$lName."}', '".$name."Controller@delete')->name('delete');
            });
        });";
            }
        }
        
        $stub = str_replace('DummyRoutes', implode($eol, $routes), $stub);
        
        return $stub;
    }
}
