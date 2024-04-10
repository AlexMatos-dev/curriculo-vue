<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscriptionsTable extends Migration {

	public function up()
	{
		Schema::create('subscriptions', function(Blueprint $table) {
			$table->bigIncrements('subscription_id', true);
			$table->bigInteger('person_id')->unsigned();
			$table->bigInteger('payment_id')->unsigned();
			$table->integer('plan_id')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('subscriptions');
	}
}