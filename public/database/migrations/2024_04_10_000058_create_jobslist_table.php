<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobsListTable extends Migration
{

	public function up()
	{
		Schema::create('jobslist', function (Blueprint $table)
		{
			$table->bigIncrements('job_id', true);
			$table->bigInteger('company_id')->unsigned();
			$table->unsignedBigInteger('job_modality_id');
			$table->unsignedInteger('job_country')->nullable();
			$table->string('job_title', 300);
			$table->string('job_state', 300)->nullable();
			$table->string('job_city', 300)->nullable();
			$table->unsignedInteger('job_seniority')->nullable();
			$table->decimal('job_salary', 10, 2)->default('0.0');
			$table->string('job_description', 500);
			$table->integer('experience_in_months')->nullable();
			$table->string('job_experience_description', 500)->nullable();
			$table->text('job_benefits', 3000)->nullable();
			$table->text('job_offer', 3000)->nullable();
			$table->text('job_requirements', 3000)->nullable();
			$table->unsignedInteger('payment_type')->nullable();
			$table->unsignedInteger('job_contract')->nullable();
			$table->unsignedInteger('job_period')->nullable();
			$table->unsignedInteger('working_visa')->nullable();
			$table->unsignedInteger('wage_currency')->nullable();
			$table->unsignedBigInteger('profession_for_job')->nullable();
			$table->unsignedBigInteger('profession_suggestion')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('jobslist');
	}
}
