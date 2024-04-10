<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentsTable extends Migration {

	public function up()
	{
		Schema::create('payments', function(Blueprint $table) {
			$table->bigIncrements('payment_id', true);
			$table->bigInteger('order_id')->unsigned();
			$table->bigInteger('person_id')->unsigned();
			$table->string('payment_method', 50);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('payments');
	}
}