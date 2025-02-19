<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('state')->default('pending');
            $table->string('user_type')->default('patient');
            $table->string('fname');
            $table->string('lname');
            $table->date('birthday');
            $table->string('gendere');
            $table->string('address');
            $table->string('phone');
            $table->string('nationality');
            $table->string('national_id')->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->longText('notes')->nullable();
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
};
