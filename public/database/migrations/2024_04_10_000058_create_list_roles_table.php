<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListRolesTable extends Migration {

	public function up()
	{
		Schema::create('list_roles', function(Blueprint $table) {
			$table->increments('lroles_id', true);
			$table->string('lroles_name', 150);
			$table->string('lroles_permissions', 300);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('list_roles');
	}
}