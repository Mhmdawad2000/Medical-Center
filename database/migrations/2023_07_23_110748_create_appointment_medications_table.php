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
        Schema::create('appointment_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Appointment::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Medication::class)->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('appointment_medications');
    }
};
