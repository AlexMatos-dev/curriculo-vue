<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompaniesTable extends Migration {

	public function up()
	{
		Schema::create('companies', function(Blueprint $table) {
			$table->bigIncrements('company_id', true);
			$table->string('company_slug', 150);
			$table->string('company_register_number', 100);
			$table->string('company_name', 300);
			$table->string('company_type', 50);
			$table->binary('company_logo')->nullable();
			$table->binary('company_cover_photo')->nullable();
			$table->string('company_video', 150)->nullable();
			$table->string('company_email', 150)->unique();
			$table->string('company_phone', 20)->nullable();
			$table->string('company_website', 100)->nullable();
			$table->string('company_description', 500)->nullable();
			$table->integer('company_number_employees')->default('1');
			$table->longText('company_benefits')->nullable();
			$table->tinyInteger('paying')->default(0);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('companies');
	}
}