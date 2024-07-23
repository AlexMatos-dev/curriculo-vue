<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCertificationsTable extends Migration {

	public function up()
	{
		Schema::create('certifications', function(Blueprint $table) {
			$table->bigIncrements('certifi_id', true);
			$table->bigInteger('cercurriculum_id')->unsigned();
			$table->string('certification_name', 150);
			$table->string('cerissuing_organization', 200);
			$table->datetime('cerissue_date');
			$table->integer('cert_hours')->default('1');
			$table->string('cerdescription', 500)->nullable();
			$table->string('cerlink', 100)->nullable();
			$table->unsignedInteger('certification_type')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('certifications');
	}
}