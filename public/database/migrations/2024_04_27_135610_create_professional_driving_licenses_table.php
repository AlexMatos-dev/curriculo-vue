<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('professional_driving_licenses', function (Blueprint $table) {
            $table->bigIncrements('professional_driving_license');
            $table->unsignedInteger('driving_license');
            $table->unsignedInteger('country');
            $table->unsignedBigInteger('professional_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_driving_licenses');
    }
};
