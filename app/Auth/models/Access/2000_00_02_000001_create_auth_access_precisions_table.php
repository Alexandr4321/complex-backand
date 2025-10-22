<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthAccessPrecisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth__access_precisions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('accessId')->unsigned();
            $table->integer('dataId')->unsigned();
            $table->string('dataType', 63);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth__access_precisions');
    }
}
