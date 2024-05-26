<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobsAppliedsTable extends Migration {

	public function up()
	{
		Schema::create('jobs_applieds', function(Blueprint $table) {
			$table->bigIncrements('applied_id', true);
			$table->bigInteger('job_id')->unsigned();
			$table->bigInteger('professional_id')->unsigned();
			$table->string('status', 10);
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('jobs_applieds');
	}
}