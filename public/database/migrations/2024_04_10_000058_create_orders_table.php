<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration {

	public function up()
	{
		Schema::create('orders', function(Blueprint $table) {
			$table->bigIncrements('order_id', true);
			$table->bigInteger('person_id')->unsigned();
			$table->string('person_type', 50);
			$table->integer('plan_id')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('orders');
	}
}