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
        Schema::create('job_driving_licenses', function (Blueprint $table) {
            $table->bigIncrements('job_driving_license');
            $table->unsignedInteger('driving_license');
            $table->unsignedInteger('country')->nullable();
            $table->unsignedBigInteger('job_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_driving_licenses');
    }
};
