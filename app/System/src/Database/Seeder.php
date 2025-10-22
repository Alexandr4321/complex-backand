<?php

namespace App\System\Database;

use Illuminate\Database\Seeder as BaseSeeder;
use Illuminate\Support\Facades\Artisan;

class Seeder extends BaseSeeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('passport:install');

        // load seeds from modules
        foreach (modules() as $module) {
            foreach ($module['seeders'] as $seeder) {
                $this->call($seeder['class']);
            }
        }
    }
}
