<?php

use App\Base\Models\Translation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base__translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('localeId')->unsigned();
            $table->integer('ownerId')->unsigned()->nullable();
            $table->string('ownerType')->nullable();
            $table->integer('tagId')->unsigned()->nullable();
            
            $table->string('field', Translation::getMax('field'))->nullable();
            $table->string('value', Translation::getMax('value'));
            
            $table->timestamp('createdAt');
            $table->timestamp('updatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('base__translations');
    }
}
