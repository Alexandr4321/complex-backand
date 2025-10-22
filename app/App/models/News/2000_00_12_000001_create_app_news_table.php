<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app__news', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('complexId')->nullable();
            $table->string('title')->nullable();
            $table->string('content')->nullable();
            $table->boolean('active')->default(false)->nullable();
            $table->timestamp('createdAt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app__news');
    }
}
