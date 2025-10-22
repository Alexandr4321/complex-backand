<?php

use App\Base\Models\Locale;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateLocalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base__locales', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('name', 63)->unique();
            $table->string('title', 127);
            $table->integer('isHidden')->default(false);
            
            $table->timestamp('createdAt');
            $table->timestamp('updatedAt')->nullable();
        });
    
        $items = [
            [ 'name' => 'ru', 'title' => 'Русский', ],
            [ 'name' => 'en', 'title' => 'English', ],
            [ 'name' => 'kz', 'title' => 'Казакша', ],
        ];
    
        foreach ($items as $item) {
            Locale::create($item);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('base__locales');
    }
}
