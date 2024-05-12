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
        Schema::create('companies_social_networks_types', function (Blueprint $table) {
            $table->increments('social_network_type_id')->primary();
            $table->string('name', 100);
            $table->string('description', 200)->nullable();
            $table->unsignedBigInteger('author_company_id')->nullable();
            $table->boolean('validated')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies_social_networks_types');
    }
};
