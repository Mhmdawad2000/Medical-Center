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
        Schema::create('secertary_scheduals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Secertary::class)->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('secertary_scheduals');
    }
};
