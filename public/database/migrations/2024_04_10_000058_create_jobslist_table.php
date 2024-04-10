<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobsListTable extends Migration {

	public function up()
	{
		Schema::create('jobslist', function(Blueprint $table) {
			$table->bigIncrements('job_id', true);
			$table->bigInteger('company_id')->unsigned();
			$table->string('job_model', 300)->nullable();
			$table->string('job_location', 300);
			$table->bigInteger('job_city')->nullable();
			$table->string('job_seniority', 100);
			$table->decimal('job_salary', 10,2)->default('0.0');
			$table->string('job_description', 500);
			$table->string('job_english_level', 100);
			$table->string('job_experience', 100);
			$table->text('job_benefits')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('jobslist');
	}
}