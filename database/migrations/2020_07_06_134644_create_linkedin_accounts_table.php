<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinkedinAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linkedin_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->text("linkedin_access_token");
            $table->string("linkedin_id");
            $table->json("accounts");
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
        Schema::dropIfExists('linkedin_accounts');
    }
}
