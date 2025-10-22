<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppApartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app__apartment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userId')->nullable();
            $table->integer('number')->nullable(); // Заголовок объявления
            $table->float('apartmentArea')->nullable(); // Адрес
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
        Schema::dropIfExists('app__apartment');
    }
}
