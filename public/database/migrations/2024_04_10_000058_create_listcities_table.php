<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListcitiesTable extends Migration {

	public function up()
	{
		Schema::create('listcities', function(Blueprint $table) {
			$table->bigIncrements('lcity_id', true);
			$table->string('lcity_name', 150);
			$table->integer('lcitstates_id')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('listcities');
	}
}