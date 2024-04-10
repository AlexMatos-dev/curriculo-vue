<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListcountriesTable extends Migration {

	public function up()
	{
		Schema::create('listcountries', function(Blueprint $table) {
			$table->increments('lcountry_id', true);
			$table->string('lcountry_name', 150);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('listcountries');
	}
}