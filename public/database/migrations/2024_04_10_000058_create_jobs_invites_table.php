<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobsInvitesTable extends Migration {

	public function up()
	{
		Schema::create('jobs_invites', function(Blueprint $table) {
			$table->bigIncrements('invite_id', true);
			$table->bigInteger('job_id')->unsigned();
			$table->bigInteger('company_id')->unsigned();
			$table->bigInteger('professional_id')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('jobs_invites');
	}
}