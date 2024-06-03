<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listprofessions', function (Blueprint $table) {
            $table->bigIncrements('lprofession_id', true);
            $table->string('profession_name', 200);
            $table->unsignedBigInteger('parent_profession_id')->nullable();
            $table->tinyInteger('valid_profession')->default(1);
            $table->unsignedBigInteger('person_id')->nullable();
            $table->unsignedInteger('profession_category_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listprofessions');
    }
};
