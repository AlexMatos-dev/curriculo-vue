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
        Schema::table('companies', function($table) {
            $table->string('company_ddi', 10)->nullable();
        });
        Schema::table('professionals', function($table) {
            $table->string('professional_ddi', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function($table) {
            $table->dropColumn('company_ddi');
        });
        Schema::table('professionals', function($table) {
            $table->dropColumn('professional_ddi');
        });
    }
};
