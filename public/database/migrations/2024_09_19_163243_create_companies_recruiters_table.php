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
        Schema::create('companies_recruiters', function (Blueprint $table) {
            $table->bigIncrements('company_recruiter_id', true);
            $table->unsignedBigInteger('recruiter_id');
            $table->unsignedBigInteger('company_id');
            $table->string('status', 20)->nullable();
            $table->timestamps();
        });
        Schema::table('companies_recruiters', function (Blueprint $table)
		{
			$table->foreign('recruiter_id')->references('recruiter_id')->on('recruiters')
				->onDelete('restrict')
				->onUpdate('restrict');
            $table->foreign('company_id')->references('company_id')->on('companies')
				->onDelete('restrict')
				->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies_recruiters', function (Blueprint $table)
		{
			$table->dropForeign('companies_recruiters_recruiter_id_foreign');
            $table->dropForeign('companies_recruiters_company_id_foreign');
		});
        Schema::dropIfExists('companies_recruiters');
    }
};
