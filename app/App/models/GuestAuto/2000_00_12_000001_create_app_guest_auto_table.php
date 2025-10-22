<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppGuestAutoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app__guest_auto', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userId')->nullable();
            $table->string('number')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->nullable();
            $table->string('brand')->nullable();
            $table->timestamp('createdAt'); // Дата и время создания записи
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app__guest_auto');
    }
}
