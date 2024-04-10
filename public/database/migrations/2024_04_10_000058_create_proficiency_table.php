<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProficiencyTable extends Migration {

	public function up()
	{
		Schema::create('proficiency', function(Blueprint $table) {
			$table->integer('proficiency_id', true)->unsigned();
			$table->string('proficiency_level', 100);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('proficiency');
	}
}