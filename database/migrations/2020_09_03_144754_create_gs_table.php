<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // client specify number of client available in the free version
        // ddays specify number of days available in the free version
        Schema::create('gs', function (Blueprint $table) {
            $table->id();
            $table->integer('clients');
            $table->integer('days');
            $table->integer('remove_social_media'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gs');
    }
}
