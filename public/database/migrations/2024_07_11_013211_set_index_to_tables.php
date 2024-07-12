<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->index('tags_id');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->index('tags_name');
        });

        Schema::table('listlangues', function (Blueprint $table) {
            $table->index('llangue_id');
        });

        Schema::table('proficiency', function (Blueprint $table) {
            $table->index('proficiency_id');
        });

        Schema::table('type_visas', function (Blueprint $table) {
            $table->index('typevisas_id');
        });

        Schema::table('translations', function (Blueprint $table) {
            $table->index('en');
        });

        Schema::table('job_contracts', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('job_periods', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('working_visas', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('job_modalities', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('listcountries', function (Blueprint $table) {
            $table->index('lcountry_name');
        });

        Schema::table('certification_types', function (Blueprint $table) {
            $table->index('certification_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex(['tags_id']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex(['tags_name']);
        });

        Schema::table('listlangues', function (Blueprint $table) {
            $table->dropIndex(['llangue_id']);
        });

        Schema::table('proficiency', function (Blueprint $table) {
            $table->dropIndex(['proficiency_id']);
        });

        Schema::table('type_visas', function (Blueprint $table) {
            $table->dropIndex(['typevisas_id']);
        });

        Schema::table('translations', function (Blueprint $table) {
            $table->dropIndex(['en']);
        });

        Schema::table('job_contracts', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('job_periods', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('working_visas', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('job_modalities', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('listcountries', function (Blueprint $table) {
            $table->dropIndex(['lcountry_name']);
        });
        
        Schema::table('certification_types', function (Blueprint $table) {
            $table->dropIndex(['certification_type']);
        });
    }
};
