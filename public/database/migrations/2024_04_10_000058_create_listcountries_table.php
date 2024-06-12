<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListcountriesTable extends Migration {

	public function up()
	{
		Schema::create('listcountries', function(Blueprint $table) {
			$table->increments('lcountry_id', true);
			$table->string('lcountry_name', 150);
			$table->string('lcountry_acronyn')->nullable();
			$table->string('ddi', 50);
			$table->mediumText('flag')->charset('binary')->nullable();
			$table->string('spokenLanguages', 300)->default('[]');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('listcountries');
	}
}