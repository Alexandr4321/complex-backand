<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base__history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userId')->unsigned()->nullable();
            $table->integer('accessId')->unsigned()->nullable();
            $table->integer('targetId')->unsigned()->nullable();
            $table->string('targetType')->nullable();
    
            $table->enum('action', [ 'read', 'edit', 'create', 'delete', 'register', 'login', 'try', ]);
            $table->json('data')->nullable();
            $table->string('ip', 30)->nullable();
            $table->string('host', 255)->nullable();
            $table->string('userAgent', 255)->nullable();
            
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
        Schema::dropIfExists('base__history');
    }
}
