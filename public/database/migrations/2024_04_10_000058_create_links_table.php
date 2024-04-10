<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLinksTable extends Migration {

	public function up()
	{
		Schema::create('links', function(Blueprint $table) {
			$table->bigIncrements('link_id', true);
			$table->bigInteger('curriculum_id')->unsigned();
			$table->string('link_type', 100);
			$table->string('url', 100);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('links');
	}
}