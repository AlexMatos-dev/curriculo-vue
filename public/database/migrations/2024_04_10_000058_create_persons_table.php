<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePersonsTable extends Migration {

	public function up()
	{
		Schema::create('persons', function(Blueprint $table) {
			$table->bigIncrements('person_id', true);
			$table->string('person_username', 300);
			$table->string('person_email', 200)->unique();
			$table->string('person_password', 80);
			$table->string('person_ddi', 10)->nullable();
			$table->string('person_phone', 20)->nullable();
			$table->unsignedInteger('person_langue');
			$table->dateTime('last_login')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('persons');
	}
}