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
        Schema::create('cummon_currency', function (Blueprint $table)
        {
            $table->id();
            $table->string('currency', 5)->unique();
            $table->string('currency_symbol', 10);
            $table->string('currency_name', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cummon_currency');
    }
};
