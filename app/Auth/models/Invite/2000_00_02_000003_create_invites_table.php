<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth__invites', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invitedId')->unsigned();
            $table->enum('invitedType', [ \App\Auth\Models\User::class, \App\Company\Models\Company::class ]);
            $table->uuid('token')->unique();
            $table->string('contact', 255);
            $table->enum('contactType', [ 'email', 'phone', ]);
            $table->integer('inviterId')->unsigned()->nullable();
            $table->enum('inviterType', [ \App\Auth\Models\User::class, \App\Company\Models\Company::class ])->nullable();
            $table->timestamp('resolvedAt')->nullable();
            $table->timestamp('sendAt')->nullable();
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
        Schema::dropIfExists('auth__invites');
    }
}
