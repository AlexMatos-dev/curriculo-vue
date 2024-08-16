<?php

use App\Models\JobList;
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
			$table->decimal('minimum_wage', 10, 2)->default('0.0');
			$table->decimal('max_wage', 10, 2)->default('0.0');
			$table->text('job_description', 5000)->nullable();
			$table->integer('experience_in_months')->nullable();
			$table->text('job_experience_description', 5000)->nullable();
			$table->text('job_benefits', 5000)->nullable();
			$table->text('job_offer', 5000)->nullable();
			$table->text('job_requirements', 5000)->nullable();
			$table->unsignedInteger('payment_type')->nullable();
			$table->unsignedInteger('job_contract')->nullable();
			$table->unsignedInteger('job_period')->nullable();
			$table->unsignedInteger('working_visa')->nullable();
			$table->unsignedInteger('wage_currency')->nullable();
			$table->unsignedBigInteger('profession_for_job')->nullable();
			$table->unsignedInteger('job_language');
			$table->string('job_status', '10')->default(JobList::DRAFT_JOB);
			$table->string('contact_email', 300)->nullable();
			$table->string('contact_name', 300)->nullable();
			$table->string('contact_website', 300)->nullable();
			$table->string('contact_phone', 20)->nullable();
			$table->string('ddi', 10)->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('jobslist');
	}
}
