<?php

namespace App\System\Providers;

use Illuminate\Support\Facades\Broadcast;
use App\Laravel\Providers\BroadcastServiceProvider as ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
}
