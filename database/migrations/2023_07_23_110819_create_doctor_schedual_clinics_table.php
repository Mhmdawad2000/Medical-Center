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
        Schema::create('doctor_schedual_clinics', function (Blueprint $table) {
            $table->id();
            $table->string('state')->default('enabled');
            $table->foreignIdFor(\App\Models\Doctor::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Clinic::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Schedual::class)->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('doctor_schedual_clinics');
    }
};
