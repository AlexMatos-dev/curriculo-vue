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
        Schema::create('job_visas', function (Blueprint $table) {
            $table->bigIncrements('job_visa_id', true);
            $table->unsignedBigInteger('joblist_id');
            $table->unsignedInteger('visas_type_id');
            $table->unsignedInteger('country_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_visas');
    }
};
