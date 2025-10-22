<?php

namespace App\System\Generator\Providers;

use App\System\Generator\Console\ModuleMakeCommand;
use App\System\Generator\Console\RoutesMakeCommand;
use App\System\Generator\Console\SeederDatabaseMakeCommand;
use App\System\Generator\Console\Model\ControllerMakeCommand;
use App\System\Generator\Console\Model\FactoryMakeCommand;
use App\System\Generator\Console\Model\ModelMakeCommand;
use App\System\Generator\Console\Model\PolicyMakeCommand;
use App\System\Generator\Console\Model\RequestMakeCommand;
use App\System\Generator\Console\Model\ResourceMakeCommand;
use App\System\Generator\Console\Model\SeederMakeCommand;
use App\System\Generator\Console\Model\TestMakeCommand;
use App\System\Generator\Console\Model\MigrationMakeCommand;
use Illuminate\Foundation\Providers\ArtisanServiceProvider as BaseServiceProvider;

class ArtisanServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrapping the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands(ModuleMakeCommand::class);
        $this->commands(ControllerMakeCommand::class);
        $this->commands(FactoryMakeCommand::class);
        $this->commands(ModelMakeCommand::class);
        $this->commands(PolicyMakeCommand::class);
        $this->commands(RequestMakeCommand::class);
        $this->commands(ResourceMakeCommand::class);
        $this->commands(MigrationMakeCommand::class);
        $this->commands(TestMakeCommand::class);
        $this->commands(RoutesMakeCommand::class);
        $this->commands(SeederMakeCommand::class);
        $this->commands(SeederDatabaseMakeCommand::class);
    }
}
