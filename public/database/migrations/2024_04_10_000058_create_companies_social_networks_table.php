<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompaniesSocialNetworksTable extends Migration {

	public function up()
	{
		Schema::create('companies_social_networks', function(Blueprint $table) {
			$table->bigIncrements('social_network_id', true);
			$table->string('social_network_profile', 150);
			$table->bigInteger('company_id')->unsigned();
			$table->unsignedInteger('social_network_type_id');
		});
	}

	public function down()
	{
		Schema::drop('companies_social_networks');
	}
}