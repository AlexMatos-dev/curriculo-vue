<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReferencesTable extends Migration {

	public function up()
	{
		Schema::create('references', function(Blueprint $table) {
			$table->bigIncrements('reference_id', true);
			$table->bigInteger('refcurriculum_id')->unsigned();
			$table->string('reference_name', 150);
			$table->string('reference_email', 100);
			$table->string('refrelationship', 100)->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('references');
	}
}