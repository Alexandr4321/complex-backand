<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth__users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName')->nullable();
            $table->string('lastName')->nullable();
            $table->string('surName')->nullable();

            $table->integer('complexId')->nullable();
            $table->integer('homeId')->nullable();
            $table->integer('entranceId')->nullable();
            $table->integer('floorId')->nullable();
            $table->integer('apartmentId')->nullable();

            $table->string('login')->nullable();
            $table->string('password')->nullable();
            $table->string('phone', 20)->nullable();

            $table->boolean('isAdmin')->default(false);
            $table->boolean('isRegistered')->default(true);
            $table->string('phoneVerifyToken', 10)->nullable();
            $table->timestamp('phoneVerifiedAt')->nullable();
            $table->timestamp('deletedAt')->nullable();
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
        Schema::dropIfExists('auth__users');
    }
}
