<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExperiencesTable extends Migration {

	public function up()
	{
		Schema::create('experiences', function(Blueprint $table) {
			$table->bigIncrements('experience_id', true);
			$table->bigInteger('excurriculum_id')->unsigned();
			$table->string('exjob_title', 100);
			$table->string('excompany_name', 150);
			$table->datetime('exstart_date');
			$table->datetime('exend_date');
			$table->string('exdescription', 500)->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('experiences');
	}
}