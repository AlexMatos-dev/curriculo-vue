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
        Schema::create('job_certifications', function (Blueprint $table) {
            $table->bigIncrements('job_certification', true);
            $table->unsignedBigInteger('joblist_id');
            $table->unsignedInteger('certification_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_certifications');
    }
};
