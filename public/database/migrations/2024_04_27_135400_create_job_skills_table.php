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
        Schema::create('job_skills', function (Blueprint $table) {
            $table->bigIncrements('job_skill_id', true);
            $table->unsignedBigInteger('joblist_id');
            $table->unsignedBigInteger('tag_id');
            $table->unsignedInteger('proficiency_id');
            $table->unsignedBigInteger('suggestion_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_skills');
    }
};
