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
        Schema::create('companies_admins', function (Blueprint $table) {
            $table->bigIncrements('company_admin_id', true);
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('person_id');
            $table->tinyInteger('has_privilegies')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies_admins');
    }
};
