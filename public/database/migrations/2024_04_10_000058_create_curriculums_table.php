<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCurriculumsTable extends Migration {

	public function up()
	{
		Schema::create('curriculums', function(Blueprint $table) {
			$table->bigIncrements('curriculum_id', true);
			$table->bigInteger('cprofes_id')->unsigned();
			$table->integer('clengua_id')->unsigned();
			$table->string('curriculum_type', 50);
            $table->mediumText('curriculum_file', 2097152)->charset('binary')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('curriculums');
	}
}