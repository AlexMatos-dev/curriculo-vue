<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListlanguesTable extends Migration {

	public function up()
	{
		Schema::create('listlangues', function(Blueprint $table) {
			$table->increments('llangue_id', true);
			$table->string('llangue_name', 150);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('listlangues');
	}
}