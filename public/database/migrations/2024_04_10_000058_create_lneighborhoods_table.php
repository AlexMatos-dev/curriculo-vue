<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLneighborhoodsTable extends Migration {

	public function up()
	{
		Schema::create('lneighborhoods', function(Blueprint $table) {
			$table->bigIncrements('lneighborhood_id', true);
			$table->string('lneighborhood_name', 150);
			$table->bigInteger('lneigcity_id')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('lneighborhoods');
	}
}