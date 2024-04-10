<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfessionalsTable extends Migration {

	public function up()
	{
		Schema::create('professionals', function(Blueprint $table) {
			$table->bigIncrements('professional_id', true);
			$table->string('professional_slug', 150);
			$table->string('professional_name', 250);
			$table->string('professional_email', 150)->unique();
			$table->string('professional_phone', 20)->nullable();
			$table->binary('professional_photo')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('professionals');
	}
}