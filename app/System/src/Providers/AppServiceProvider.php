<?php

namespace App\System\Providers;

use App\Laravel\Providers\AppServiceProvider as ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Session\TokenMismatchException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $migrations = [];
        foreach (modules() as $moduleName => $module) {
            foreach (glob(app_path("$moduleName/models/*/Factories")) as $path) {
                $this->app->make(Factory::class)->load($path);
            }

            view()->addNamespace(strtolower($moduleName), app_path("$moduleName/resources/views"));

            foreach (glob(app_path("$moduleName/config/*.php")) as $file) {
                $this->mergeConfigFrom($file, strtolower($module));
            }

            foreach ($module['migrations'] as $item) {
                $migrations[] = $item['path'];
            }
        }

        $this->loadMigrationsFrom(array_merge([database_path('migrations')], $migrations));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
