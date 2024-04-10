<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDatapersonTable extends Migration {

	public function up()
	{
		Schema::create('dataperson', function(Blueprint $table) {
			$table->bigIncrements('dpperson_id', true);
			$table->bigInteger('dpprofes_id')->unsigned();
			$table->datetime('dpdate_of_birth');
			$table->integer('dpgender')->unsigned();
			$table->string('dpaddress', 300)->nullable();
			$table->bigInteger('dpcity_id')->unsigned();
			$table->integer('dpstate_id')->unsigned()->nullable();
			$table->string('dppostal_code', 20)->nullable();
			$table->integer('dpcountry_id')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('dataperson');
	}
}