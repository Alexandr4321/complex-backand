<?php

use App\Auth\Models\Permit;
use App\Auth\Models\Grant;
use App\Auth\Models\PermitGrant;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermitsGrantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth__permits_grants', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('permitId')->unsigned();
            $table->integer('grantId')->unsigned();
            $table->boolean('isGlobal')->default(false);
        });
    
        foreach (config('modules.permits_grants') as $permName => $grants) {
            $perm = Permit::query()->where('name', $permName)->first();
            foreach ($grants as $grantName => $isGlobal) {
                $grant = Grant::query()->where('name', $grantName)->first();
                $pg = new PermitGrant();
                $pg->permitId = $perm->id;
                $pg->grantId = $grant->id;
                $pg->isGlobal = $isGlobal;
                $pg->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth__permits_grants');
    }
}
