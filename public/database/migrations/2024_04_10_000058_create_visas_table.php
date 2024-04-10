<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVisasTable extends Migration {

	public function up()
	{
		Schema::create('visas', function(Blueprint $table) {
			$table->bigIncrements('visas_id', true);
			$table->bigInteger('vicurriculum_id')->unsigned();
			$table->integer('vicountry_id')->unsigned();
			$table->integer('visa_type')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('visas');
	}
}