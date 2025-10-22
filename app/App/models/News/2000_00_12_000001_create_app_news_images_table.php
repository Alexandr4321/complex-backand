<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppNewsImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('app__news_images');
        Schema::create('app__news_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('newsId');
            $table->integer('userId');
            $table->integer('fileId');
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
        Schema::dropIfExists('app__news_images');
    }
}
