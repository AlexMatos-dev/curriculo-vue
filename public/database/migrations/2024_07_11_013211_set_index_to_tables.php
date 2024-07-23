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
        /**
         * Start of Jobslist
         */
        Schema::table('jobslist', function (Blueprint $table) {
            $table->index('job_id');
            $table->index('created_at');
            $table->index('company_id');
        });
         /**
          * END of Jobslist
          */

        Schema::table('companies', function (Blueprint $table) {
            $table->index('paying');
            $table->index('company_id');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->index('tags_id');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->index('tags_name');
        });

        Schema::table('listlangues', function (Blueprint $table) {
            $table->index('llangue_id');
            $table->index('llangue_name');
        });

        Schema::table('proficiency', function (Blueprint $table) {
            $table->index('proficiency_id');
            $table->index('proficiency_level');
        });

        Schema::table('type_visas', function (Blueprint $table) {
            $table->index('typevisas_id');
            $table->index('type_name');
        });

        Schema::table('translations', function (Blueprint $table) {
            $table->index('en');
        });

        Schema::table('job_contracts', function (Blueprint $table) {
            $table->index('job_contract');
            $table->index('name');
        });

        Schema::table('job_periods', function (Blueprint $table) {
            $table->index('job_period');
            $table->index('name');
        });

        Schema::table('working_visas', function (Blueprint $table) {
            $table->index('working_visa');
            $table->index('name');
        });

        Schema::table('job_modalities', function (Blueprint $table) {
            $table->index('job_modality_id');
            $table->index('name');
        });

        Schema::table('listcountries', function (Blueprint $table) {
            $table->index('lcountry_id');
            $table->index('lcountry_name');
        });

        Schema::table('certification_types', function (Blueprint $table) {
            $table->index('certification_type');
        });

        Schema::table('listprofessions', function (Blueprint $table) {
            $table->index('lprofession_id');
            $table->index('profession_name');
        });

        Schema::table('common_currencies', function (Blueprint $table) {
            $table->index('common_currency_id');
            $table->index('currency');
        });

        Schema::table('company_types', function (Blueprint $table) {
            $table->index('company_type_id');
            $table->index('name');
        });

        Schema::table('job_payment_types', function (Blueprint $table) {
            $table->index('job_payment_type');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /**
         * Start of Jobslist
         */
        Schema::table('jobslist', function (Blueprint $table) {
            $table->dropIndex(['job_id']);
            $table->dropIndex(['created_at']);
        });
         /**
          * END of Jobslist
          */

        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['paying']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex(['tags_id']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex(['tags_name']);
        });

        Schema::table('listlangues', function (Blueprint $table) {
            $table->dropIndex(['llangue_id']);
            $table->dropIndex(['llangue_name']);
        });

        Schema::table('proficiency', function (Blueprint $table) {
            $table->dropIndex(['proficiency_id']);
            $table->dropIndex(['proficiency_level']);
        });

        Schema::table('type_visas', function (Blueprint $table) {
            $table->dropIndex(['typevisas_id']);
            $table->dropIndex(['type_name']);
        });

        Schema::table('translations', function (Blueprint $table) {
            $table->dropIndex(['en']);
        });

        Schema::table('job_contracts', function (Blueprint $table) {
            $table->dropIndex(['job_contract']);
            $table->dropIndex(['name']);
        });

        Schema::table('job_periods', function (Blueprint $table) {
            $table->dropIndex(['job_period']);
            $table->dropIndex(['name']);
        });

        Schema::table('working_visas', function (Blueprint $table) {
            $table->dropIndex(['working_visa']);
            $table->dropIndex(['name']);
        });

        Schema::table('job_modalities', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['job_modality_id']);
        });

        Schema::table('listcountries', function (Blueprint $table) {
            $table->dropIndex(['lcountry_id']);
            $table->dropIndex(['lcountry_name']);
        });
        
        Schema::table('certification_types', function (Blueprint $table) {
            $table->dropIndex(['certification_type']);
        });

        Schema::table('listprofessions', function (Blueprint $table) {
            $table->dropIndex(['lprofession_id']);
            $table->dropIndex(['profession_name']);
        });

        Schema::table('common_currencies', function (Blueprint $table) {
            $table->dropIndex(['common_currency_id']);
            $table->dropIndex(['currency']);
        });

        Schema::table('company_types', function (Blueprint $table) {
            $table->dropIndex(['company_type_id']);
            $table->dropIndex(['name']);
        });

        Schema::table('job_payment_types', function (Blueprint $table) {
            $table->dropIndex(['job_payment_type']);
            $table->dropIndex(['name']);
        });
    }
};
