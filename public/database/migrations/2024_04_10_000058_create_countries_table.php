<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCountriesTable extends Migration {

	public function up()
	{
		Schema::create('countries', function(Blueprint $table) {
			$table->increments('country_id', true);
			$table->bigInteger('curriculum_id')->unsigned();
			$table->integer('country_name')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('countries');
	}
}