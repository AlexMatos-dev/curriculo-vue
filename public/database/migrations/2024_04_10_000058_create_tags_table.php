<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTagsTable extends Migration {

	public function up()
	{
		Schema::create('tags', function(Blueprint $table) {
			$table->bigIncrements('tags_id', true);
			$table->string('tags_name', 150);
			$table->unsignedBigInteger('suggestion_id')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tags');
	}
}