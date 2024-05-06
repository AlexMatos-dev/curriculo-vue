<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSkillsTable extends Migration {

	public function up()
	{
		Schema::create('skills', function(Blueprint $table) {
			$table->bigIncrements('skill_id', true);
			$table->bigInteger('skcurriculum_id')->unsigned();
			$table->bigInteger('skill_name')->unsigned();
			$table->integer('skproficiency_level')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('skills');
	}
}