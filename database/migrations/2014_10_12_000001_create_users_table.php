<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('business_name')->nullable();
            $table->string('category')->nullable();
            $table->bigInteger('employees')->nullable();
            $table->string('phone')->nullable();
            $table->string('image')->nullable();
            $table->boolean('notification')->default(true);
            $table->boolean('status')->default(true);
            $table->foreignId('plan_id')->constrained();
            $table->dateTime('started_at');
            $table->dateTime('ended_at');
            $table->boolean('expired')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
