<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTypeVisasTable extends Migration {

	public function up()
	{
		Schema::create('type_visas', function(Blueprint $table) {
			$table->increments('typevisas_id', true);
			$table->string('type_name', 150);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('type_visas');
	}
}