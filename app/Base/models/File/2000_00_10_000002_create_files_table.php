<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base__files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('authorId')->unsigned();
            $table->integer('ownerId')->unsigned()->nullable();
            $table->string('ownerType', 255)->nullable();

            $table->string('tag', 127)->nullable();
            $table->string('name', 511);
            $table->string('description', 2047)->default('');
            $table->integer('size')->nullable();
            $table->string('src', 255)->nullable();
            $table->boolean('isExternal')->default(false);
            $table->integer('position')->default(0);
            $table->boolean('isVerified')->default(false);

            $table->timestamp('createdAt');
            $table->timestamp('deletedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('base__files');
    }
}
