<?php

namespace App\System\Generator\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleMakeCommand extends Command
{
    use UseConfig;
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:module';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create full module';
    
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $modelFiles = [ 'model', 'controller', 'factory', 'migration', 'policy', 'request', 'resource', 'seeder', 'test', ];
      
        $module = Str::studly(class_basename($this->getModuleName()));
        $models = $this->getModels($this->getModuleName());
        
        foreach ($models as $name => $model) {
            foreach ($modelFiles as $modelFile) {
                $this->call("make:$modelFile", [ 'name' => $name, '--module' => $module, ]);
            }
        }
    
        $this->call('make:routes', [ '--module' => $module, ]);
        $this->call('make:seeder-database', [ '--module' => $module, ]);
    
        if ($this->option('dump')) {
            app('composer')->dumpAutoloads();
        }
        if ($this->option('migrate')) {
            Artisan::call('migrate:refresh', [ '--seed', ]);
        }
    }
    
    protected function getModuleName()
    {
        return trim($this->argument('name'));
    }
    
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the module'],
        ];
    }
    
    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['dump', 'f', InputOption::VALUE_NONE, 'Run composer dump-autoload'],
            ['migrate', 'm', InputOption::VALUE_NONE, 'Run migrations with seeds'],
        ];
    }
}
