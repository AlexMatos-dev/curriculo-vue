<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEducationsTable extends Migration {

	public function up()
	{
		Schema::create('educations', function(Blueprint $table) {
			$table->increments('education_id', true);
			$table->bigInteger('edcurriculum_id')->unsigned();
			$table->string('eddegree', 100);
			$table->string('edfield_of_study', 150);
			$table->string('edinstitution', 150);
			$table->datetime('edstart_date');
			$table->datetime('edend_date')->nullable();
			$table->string('eddescription', 400)->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('educations');
	}
}