<?php

use App\Auth\Models\Grant;
use App\Auth\Types\TypeGrant;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth__grants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 31);
            $table->string('modelType', 31)->nullable();
        });
        
        foreach (config('modules.grants') as $name => $modelType) {
            creator(Grant::class, [ 'name' => $name, 'modelType' => $modelType, ], TypeGrant::class)->create();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth__grants');
    }
}
