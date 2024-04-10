<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecruitersTable extends Migration {

	public function up()
	{
		Schema::create('recruiters', function(Blueprint $table) {
			$table->bigIncrements('recruiter_id', true);
			$table->bigInteger('company_id')->unsigned();
			$table->binary('recruiter_photo')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('recruiters');
	}
}