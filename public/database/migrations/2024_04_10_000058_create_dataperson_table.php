<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDatapersonTable extends Migration {

	public function up()
	{
		Schema::create('dataperson', function(Blueprint $table) {
			$table->bigIncrements('dpperson_id', true);
			$table->bigInteger('dpprofes_id')->unsigned();
			$table->datetime('dpdate_of_birth')->nullable();
			$table->integer('dpgender')->unsigned()->nullable();
			$table->string('dpcity', 300)->nullable();
			$table->string('dpstate', 300)->nullable();
			$table->string('dppostal_code', 20)->nullable();
			$table->integer('dpcountry_id')->unsigned()->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('dataperson');
	}
}