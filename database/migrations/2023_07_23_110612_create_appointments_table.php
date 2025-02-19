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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->foreignIdFor(\App\Models\Patient::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Doctor::class)->constrained()->onDelete('cascade');
            $table->string('description');
            $table->string('state')->default('active');
            $table->foreignIdFor(\App\Models\User::class)->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('appointments');
    }
};
