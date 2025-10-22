<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppHomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app__home', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('complexId')->nullable();
            $table->string('title')->nullable(); // Заголовок объявления
            $table->boolean('elevator')->default(false); // Наличие лифта
            $table->string('address')->nullable(); // Адрес
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
        Schema::dropIfExists('app__home');
    }
}
