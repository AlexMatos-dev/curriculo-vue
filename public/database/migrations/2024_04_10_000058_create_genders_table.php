<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGendersTable extends Migration {

	public function up()
	{
		Schema::create('genders', function(Blueprint $table) {
			$table->increments('gender_id', true)->unsigned();
			$table->string('gender_name', 100);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('genders');
	}
}