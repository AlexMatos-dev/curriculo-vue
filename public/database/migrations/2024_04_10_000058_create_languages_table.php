<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguagesTable extends Migration {

	public function up()
	{
		Schema::create('languages', function(Blueprint $table) {
			$table->bigIncrements('lang_id', true);
			$table->bigInteger('lacurriculum_id')->unsigned();
			$table->integer('lalangue_id')->unsigned();
			$table->integer('laspeaking_level')->unsigned()->nullable();
			$table->integer('lalistening_level')->unsigned()->nullable();
			$table->integer('lawriting_level')->unsigned()->nullable();
			$table->integer('lareading_level')->unsigned()->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('languages');
	}
}