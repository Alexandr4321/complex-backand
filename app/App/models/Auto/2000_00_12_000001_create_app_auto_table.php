<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppAutoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app__auto', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userId')->nullable();
            $table->string('number')->nullable(); // Заголовок объявления
            $table->string('brand')->nullable();
            $table->string('parkingSpace')->nullable();// Адрес
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
        Schema::dropIfExists('app__auto');
    }
}
