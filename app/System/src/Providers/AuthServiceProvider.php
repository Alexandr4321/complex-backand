<?php

namespace App\System\Providers;

use App\Laravel\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        foreach (modules() as $module) {
            foreach ($module['policies'] as $item) {
                $this->policies[$item['model']] = $item['class'];
            }
        }
        
        $this->registerPolicies();
    }
}
