<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecruitersTable extends Migration {

	public function up()
	{
		Schema::create('recruiters', function(Blueprint $table) {
			$table->bigIncrements('recruiter_id', true);
			$table->bigInteger('person_id')->unsigned();
			$table->bigInteger('company_id')->unsigned()->nullable();
			$table->tinyInteger('paying')->default(0);
			$table->mediumText('recruiter_photo', 2097152)->charset('binary')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('recruiters');
	}
}