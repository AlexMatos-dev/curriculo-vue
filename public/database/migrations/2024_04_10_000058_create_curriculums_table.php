<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCurriculumsTable extends Migration {

	public function up()
	{
		Schema::create('curriculums', function(Blueprint $table) {
			$table->bigIncrements('curriculum_id', true);
			$table->bigInteger('cprofes_id')->unsigned();
			$table->integer('clengua_id')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('curriculums');
	}
}