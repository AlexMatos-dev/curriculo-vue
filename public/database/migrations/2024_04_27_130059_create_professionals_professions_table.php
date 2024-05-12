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
        Schema::create('professionals_professions', function (Blueprint $table) {
            $table->bigIncrements('professional_profession_id', true);
            $table->unsignedBigInteger('professional_id');
            $table->unsignedBigInteger('lprofession_id');
            $table->dateTime('started_working_at');
            $table->string('observations', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professionals_professions');
    }
};
