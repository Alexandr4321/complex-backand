<?php

use App\Auth\Models\Permit;
use App\Auth\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $types = [
            Permit::class, Role::class,
        ];
        
        Schema::create('auth__accesses', function (Blueprint $table) use ($types) {
            $table->increments('id');
            $table->string('name', 31);
            $table->enum('type', $types);
            $table->integer('modelId')->unsigned()->nullable();
            $table->integer('ownerId')->unsigned();
            $table->string('ownerType', 63);
            $table->integer('contractorId')->unsigned()->nullable();
            $table->string('contractorType', 63)->nullable();
            
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
        Schema::dropIfExists('auth__accesses');
    }
}
