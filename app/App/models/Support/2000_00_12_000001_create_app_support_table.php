<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppSupportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app__support', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('complexId')->nullable();
            $table->string('fullName')->nullable();
            $table->string('content')->nullable();
            $table->string('answer')->nullable();
            $table->timestamp('answerAt')->nullable();
            $table->boolean('active')->default(true)->nullable();
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
        Schema::dropIfExists('app__support');
    }
}
