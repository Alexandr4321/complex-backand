<?php

use App\Auth\Models\Permit;
use App\Auth\Types\TypePermit;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth__permits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 31);
            $table->string('modelType', 31)->nullable();
        });
        
        foreach (config('modules.permits') as $name => $modelType) {
            creator(Permit::class, [ 'name' => $name, 'modelType' => $modelType, ], TypePermit::class)->create();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth__permits');
    }
}
