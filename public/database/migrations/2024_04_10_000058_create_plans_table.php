<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlansTable extends Migration {

	public function up()
	{
		Schema::create('plans', function(Blueprint $table) {
			$table->increments('plan_id', true);
			$table->string('plan_type', 50);
			$table->string('plan_name', 150);
			$table->decimal('plan_price', 10,2)->default('1.0');
			$table->integer('plan_days_period')->default('1');
			$table->integer('plan_months_period')->default('1');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('plans');
	}
}