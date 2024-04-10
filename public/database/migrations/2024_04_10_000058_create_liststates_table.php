<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListstatesTable extends Migration {

	public function up()
	{
		Schema::create('liststates', function(Blueprint $table) {
			$table->increments('lstates_id', true);
			$table->string('lstates_name', 100);
			$table->integer('lstates_parent_id')->unsigned();
			$table->integer('lstates_level')->default('0');
			$table->integer('lstacountry_id')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('liststates');
	}
}