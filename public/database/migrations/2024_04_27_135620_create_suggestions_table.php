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
        Schema::create('suggestions', function (Blueprint $table) {
            $table->bigIncrements('suggestion_id');
            $table->string('type', 100);
            $table->bigInteger('type_id');
            $table->unsignedBigInteger('author_id');
            $table->unsignedInteger('lang');
            $table->string('suggestion_name', 300);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestions');
    }
};
