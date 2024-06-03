<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfessionalsTable extends Migration {

	public function up()
	{
		Schema::create('professionals', function(Blueprint $table) {
			$table->bigIncrements('professional_id', true);
			$table->bigInteger('person_id')->unsigned();
			$table->string('professional_slug', 150)->unique();
			$table->string('professional_firstname', 250);
			$table->string('professional_lastname', 250);
			$table->string('professional_email', 150)->unique();
			$table->string('professional_phone', 20)->nullable();
			$table->mediumText('professional_photo', 2097152)->charset('binary')->nullable();
            $table->mediumText('professional_cover', 2097152)->charset('binary')->nullable();
			$table->string('professional_title')->nullable();
			$table->tinyInteger('paying')->default(0);
			$table->tinyInteger('currently_working')->default(0);
			$table->tinyInteger('avaliable_to_travel')->default(0);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('professionals');
	}
}