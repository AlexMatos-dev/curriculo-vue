<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRolesTable extends Migration {

	public function up()
	{
		Schema::create('roles', function(Blueprint $table) {
			$table->bigIncrements('roles_id', true);
			$table->integer('lroles_id')->unsigned();
			$table->bigInteger('lrperson_id')->unsigned();
			$table->bigInteger('lrprofes_id')->unsigned()->nullable();
			$table->bigInteger('lrcompan_id')->unsigned()->nullable();
			$table->bigInteger('lrrecrut_id')->unsigned()->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('roles');
	}
}