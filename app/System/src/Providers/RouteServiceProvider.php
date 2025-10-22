<?php

namespace App\System\Providers;

use App\Laravel\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = '';
    
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        foreach (modules() as $module) {
            foreach ($module['models'] as $model) {
                Route::model(strtolower($model['name']), $model['class']);
            }
        }

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapModulesApiRoutes();
        $this->mapApiRoutes();
    
        $this->mapModulesWebRoutes();
        $this->mapWebRoutes();
    }
    
    
    /**
     * Define the "web" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapModulesWebRoutes()
    {
        foreach (config('modules.imports') as $module) {
            if (file_exists(app_path("$module/routes/web.php"))) {
                Route::middleware('web')
                    ->namespace("App\\$module\Controllers\Web")
                    ->group(app_path("$module/routes/web.php"));
            }
        }
    }
    
    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapModulesApiRoutes()
    {
        foreach (config('modules.imports') as $module) {
            if (file_exists(app_path("$module/routes/api.php"))) {
                Route::prefix('api')
                    ->middleware('api')
                    ->namespace("App\\$module\Controllers\Api")
                    ->group(app_path("$module/routes/api.php"));
            }
        }
    }
}
