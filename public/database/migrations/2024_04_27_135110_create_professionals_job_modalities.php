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
        Schema::create('professionals_job_modalities', function (Blueprint $table) {
            $table->bigIncrements('professional_job_modality_id', true);
            $table->unsignedBigInteger('professional_id');
            $table->unsignedBigInteger('job_modality_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professionals_job_modalities');
    }
};
