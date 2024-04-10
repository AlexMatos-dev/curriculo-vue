<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePresentationsTable extends Migration {

	public function up()
	{
		Schema::create('presentations', function(Blueprint $table) {
			$table->bigIncrements('presentation_id', true);
			$table->bigInteger('precurriculum_id')->unsigned();
			$table->text('presentation_text');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('presentations');
	}
}