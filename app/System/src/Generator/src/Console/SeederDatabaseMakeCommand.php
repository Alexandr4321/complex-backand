<?php

namespace App\System\Generator\Console;

use Illuminate\Console\GeneratorCommand;

class SeederDatabaseMakeCommand extends GeneratorCommand
{
    use IsModuleCommand;
    
    protected $stubName = 'seeder-database.stub';
    
    protected $namespace = '\Seeds';
    
    protected $path = '/database/Seeds';
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:seeder-database';
    
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
     * Return name of the file.
     *
     * @return string
     */
    protected function getFileName()
    {
        return 'DatabaseSeeder.php';
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
        $stub = parent::buildClass($name);
        $stub = $this->replaceModel($stub);
        
        return $stub;
    }
    
    /**
     * Replace model in stub.
     *
     * @param $stub
     * @return mixed
     */
    protected function replaceModel($stub)
    {
        $seeders = [];
        
        foreach ($this->models as $name => $model) {
            $seeders[] = '$this->call('.$name.'Seeder::class);';
        }
    
        $stub = str_replace('DummySeeders', implode(PHP_EOL.'        ', $seeders), $stub);
        
        return $stub;
    }
}
