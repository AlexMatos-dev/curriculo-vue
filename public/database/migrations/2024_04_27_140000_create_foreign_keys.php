<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('persons', function(Blueprint $table) {
			$table->foreign('person_langue')->references('llangue_id')->on('listlangues')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('companies', function(Blueprint $table) {
			$table->foreign('company_type')->references('company_type_id')->on('company_types')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('professionals', function(Blueprint $table) {
			$table->foreign('person_id')->references('person_id')->on('persons')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('companies_social_networks', function(Blueprint $table) {
			$table->foreign('company_id')->references('company_id')->on('companies')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('companies_social_networks', function(Blueprint $table) {
			$table->foreign('social_network_type_id')->references('social_network_type_id')->on('companies_social_networks_types')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('companies_social_networks_types', function(Blueprint $table) {
			$table->foreign('author_company_id')->references('company_id')->on('companies')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('recruiters', function(Blueprint $table) {
			$table->foreign('person_id')->references('person_id')->on('persons')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('recruiters', function(Blueprint $table) {
			$table->foreign('company_id')->references('company_id')->on('companies')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('jobs_applieds', function(Blueprint $table) {
			$table->foreign('job_id')->references('job_id')->on('jobslist')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('jobs_applieds', function(Blueprint $table) {
			$table->foreign('professional_id')->references('professional_id')->on('professionals')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('jobslist', function(Blueprint $table) {
			$table->foreign('company_id')->references('company_id')->on('companies')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('jobslist', function(Blueprint $table) {
			$table->foreign('job_city')->references('lcity_id')->on('listcities')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('jobslist', function(Blueprint $table) {
			$table->foreign('job_country')->references('lcountry_id')->on('listcountries')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('jobslist', function(Blueprint $table) {
			$table->foreign('job_modality_id')->references('job_modality_id')->on('job_modalities')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('jobslist', function(Blueprint $table) {
			$table->foreign('job_seniority')->references('proficiency_id')->on('proficiency')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('jobslist', function(Blueprint $table) {
			$table->foreign('wage_currency')->references('common_currency_id')->on('common_currencies')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('job_skills', function(Blueprint $table) {
			$table->foreign('joblist_id')->references('job_id')->on('jobslist')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('job_skills', function(Blueprint $table) {
			$table->foreign('tag_id')->references('tags_id')->on('tags')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('job_skills', function(Blueprint $table) {
			$table->foreign('proficiency_id')->references('proficiency_id')->on('proficiency')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('job_languages', function(Blueprint $table) {
			$table->foreign('joblist_id')->references('job_id')->on('jobslist')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('job_languages', function(Blueprint $table) {
			$table->foreign('language_id')->references('llangue_id')->on('listlangues')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('job_languages', function(Blueprint $table) {
			$table->foreign('proficiency_id')->references('proficiency_id')->on('proficiency')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('job_visas', function(Blueprint $table) {
			$table->foreign('joblist_id')->references('job_id')->on('jobslist')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('job_visas', function(Blueprint $table) {
			$table->foreign('visas_type_id')->references('typevisas_id')->on('type_visas')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('job_visas', function(Blueprint $table) {
			$table->foreign('country_id')->references('lcountry_id')->on('listcountries')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('jobs_invites', function(Blueprint $table) {
			$table->foreign('job_id')->references('job_id')->on('jobslist')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('jobs_invites', function(Blueprint $table) {
			$table->foreign('company_id')->references('company_id')->on('companies')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('jobs_invites', function(Blueprint $table) {
			$table->foreign('professional_id')->references('professional_id')->on('professionals')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('subscriptions', function(Blueprint $table) {
			$table->foreign('person_id')->references('person_id')->on('persons')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('subscriptions', function(Blueprint $table) {
			$table->foreign('payment_id')->references('payment_id')->on('payments')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('subscriptions', function(Blueprint $table) {
			$table->foreign('plan_id')->references('plan_id')->on('plans')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('payments', function(Blueprint $table) {
			$table->foreign('order_id')->references('order_id')->on('orders')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('payments', function(Blueprint $table) {
			$table->foreign('person_id')->references('person_id')->on('persons')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('orders', function(Blueprint $table) {
			$table->foreign('person_id')->references('person_id')->on('persons')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('orders', function(Blueprint $table) {
			$table->foreign('plan_id')->references('plan_id')->on('plans')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('curriculums', function(Blueprint $table) {
			$table->foreign('cprofes_id')->references('professional_id')->on('professionals')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('curriculums', function(Blueprint $table) {
			$table->foreign('clengua_id')->references('llangue_id')->on('listlangues')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('languages', function(Blueprint $table) {
			$table->foreign('lacurriculum_id')->references('curriculum_id')->on('curriculums')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('languages', function(Blueprint $table) {
			$table->foreign('lalangue_id')->references('llangue_id')->on('listlangues')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('languages', function(Blueprint $table) {
			$table->foreign('laspeaking_level')->references('proficiency_id')->on('proficiency')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('languages', function(Blueprint $table) {
			$table->foreign('lalistening_level')->references('proficiency_id')->on('proficiency')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('languages', function(Blueprint $table) {
			$table->foreign('lawriting_level')->references('proficiency_id')->on('proficiency')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('languages', function(Blueprint $table) {
			$table->foreign('lareading_level')->references('proficiency_id')->on('proficiency')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('skills', function(Blueprint $table) {
			$table->foreign('skcurriculum_id')->references('curriculum_id')->on('curriculums')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('skills', function(Blueprint $table) {
			$table->foreign('skill_name')->references('tags_id')->on('tags')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('skills', function(Blueprint $table) {
			$table->foreign('skproficiency_level')->references('proficiency_id')->on('proficiency')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('educations', function(Blueprint $table) {
			$table->foreign('edcurriculum_id')->references('curriculum_id')->on('curriculums')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('educations', function(Blueprint $table) {
			$table->foreign('degree_type')->references('degree_type_id')->on('degree_types')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('educations', function(Blueprint $table) {
			$table->foreign('edfield_of_study')->references('area_of_study_id')->on('areas_of_study')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('references', function(Blueprint $table) {
			$table->foreign('refcurriculum_id')->references('curriculum_id')->on('curriculums')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('certifications', function(Blueprint $table) {
			$table->foreign('cercurriculum_id')->references('curriculum_id')->on('curriculums')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('experiences', function(Blueprint $table) {
			$table->foreign('excurriculum_id')->references('curriculum_id')->on('curriculums')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('presentations', function(Blueprint $table) {
			$table->foreign('precurriculum_id')->references('curriculum_id')->on('curriculums')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('links', function(Blueprint $table) {
			$table->foreign('curriculum_id')->references('curriculum_id')->on('curriculums')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('countries', function(Blueprint $table) {
			$table->foreign('curriculum_id')->references('curriculum_id')->on('curriculums')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('countries', function(Blueprint $table) {
			$table->foreign('country_name')->references('lcountry_id')->on('listcountries')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('liststates', function(Blueprint $table) {
			$table->foreign('lstates_parent_id')->references('lstates_id')->on('liststates')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('liststates', function(Blueprint $table) {
			$table->foreign('lstacountry_id')->references('lcountry_id')->on('listcountries')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('listcities', function(Blueprint $table) {
			$table->foreign('lcitstates_id')->references('lstates_id')->on('liststates')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('dataperson', function(Blueprint $table) {
			$table->foreign('dpprofes_id')->references('professional_id')->on('professionals')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('dataperson', function(Blueprint $table) {
			$table->foreign('dpgender')->references('gender_id')->on('genders')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('dataperson', function(Blueprint $table) {
			$table->foreign('dpcity_id')->references('lcity_id')->on('listcities')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('dataperson', function(Blueprint $table) {
			$table->foreign('dpstate_id')->references('lstates_id')->on('liststates')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('dataperson', function(Blueprint $table) {
			$table->foreign('dpcountry_id')->references('lcountry_id')->on('listcountries')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('visas', function(Blueprint $table) {
			$table->foreign('vicurriculum_id')->references('curriculum_id')->on('curriculums')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('visas', function(Blueprint $table) {
			$table->foreign('vicountry_id')->references('country_id')->on('countries')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('visas', function(Blueprint $table) {
			$table->foreign('visa_type')->references('typevisas_id')->on('type_visas')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('profiles', function(Blueprint $table) {
			$table->foreign('person_id')->references('person_id')->on('persons')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('listprofessions', function(Blueprint $table) {
			$table->foreign('parent_profession_id')->references('lprofession_id')->on('listprofessions')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('listprofessions', function(Blueprint $table) {
			$table->foreign('person_id')->references('person_id')->on('persons')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('professionals_professions', function(Blueprint $table) {
			$table->foreign('professional_id')->references('professional_id')->on('professionals')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('professionals_professions', function(Blueprint $table) {
			$table->foreign('lprofession_id')->references('lprofession_id')->on('listprofessions')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('companies_admins', function(Blueprint $table) {
			$table->foreign('company_id')->references('company_id')->on('companies')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('companies_admins', function(Blueprint $table) {
			$table->foreign('person_id')->references('person_id')->on('persons')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('professionals_job_modalities', function(Blueprint $table) {
			$table->foreign('professional_id')->references('professional_id')->on('professionals')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('professionals_job_modalities', function(Blueprint $table) {
			$table->foreign('job_modality_id')->references('job_modality_id')->on('job_modalities')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
	}

	public function down()
	{
		Schema::table('persons', function(Blueprint $table) {
			$table->dropForeign('persons_person_langue_foreign');
		});
		Schema::table('companies', function(Blueprint $table) {
			$table->dropForeign('companies_company_type_foreign');
		});
		Schema::table('professionals', function(Blueprint $table) {
			$table->dropForeign('professionals_person_id_foreign');
		});
		Schema::table('companies_social_networks', function(Blueprint $table) {
			$table->dropForeign('companies_social_networks_company_id_foreign');
		});
		Schema::table('companies_social_networks', function(Blueprint $table) {
			$table->dropForeign('companies_social_networks_social_network_type_id_foreign');
		});
		Schema::table('companies_social_networks_types', function(Blueprint $table) {
			$table->dropForeign('companies_social_networks_types_author_company_id_foreign');
		});
		Schema::table('recruiters', function(Blueprint $table) {
			$table->dropForeign('recruiters_person_id_foreign');
		});
		Schema::table('recruiters', function(Blueprint $table) {
			$table->dropForeign('recruiters_company_id_foreign');
		});
		Schema::table('jobs_applieds', function(Blueprint $table) {
			$table->dropForeign('jobs_applieds_job_id_foreign');
		});
		Schema::table('jobs_applieds', function(Blueprint $table) {
			$table->dropForeign('jobs_applieds_professional_id_foreign');
		});
		Schema::table('jobslist', function(Blueprint $table) {
			$table->dropForeign('jobslist_company_id_foreign');
		});
		Schema::table('jobslist', function(Blueprint $table) {
			$table->dropForeign('jobslist_job_city_foreign');
		});
		Schema::table('jobslist', function(Blueprint $table) {
			$table->dropForeign('jobslist_job_country_foreign');
		});
		Schema::table('jobslist', function(Blueprint $table) {
			$table->dropForeign('jobslist_job_modality_id_foreign');
		});
		Schema::table('jobslist', function(Blueprint $table) {
			$table->dropForeign('jobslist_job_seniority_foreign');
		});
		Schema::table('jobslist', function(Blueprint $table) {
			$table->dropForeign('jobslist_wage_currency_foreign');
		});
		Schema::table('job_skills', function(Blueprint $table) {
			$table->dropForeign('job_skills_joblist_id_foreign');
		});
		Schema::table('job_skills', function(Blueprint $table) {
			$table->dropForeign('job_skills_tag_id_foreign');
		});
		Schema::table('job_skills', function(Blueprint $table) {
			$table->dropForeign('job_skills_proficiency_id_foreign');
		});
		Schema::table('job_languages', function(Blueprint $table) {
			$table->dropForeign('job_languages_joblist_id_foreign');
		});
		Schema::table('job_languages', function(Blueprint $table) {
			$table->dropForeign('job_languages_language_id_foreign');
		});
		Schema::table('job_languages', function(Blueprint $table) {
			$table->dropForeign('job_languages_proficiency_id_foreign');
		});
		Schema::table('job_visas', function(Blueprint $table) {
			$table->dropForeign('job_visas_joblist_id_foreign');
		});
		Schema::table('job_visas', function(Blueprint $table) {
			$table->dropForeign('job_visas_visas_type_id_foreign');
		});
		Schema::table('job_visas', function(Blueprint $table) {
			$table->dropForeign('job_visas_country_id_foreign');
		});
		Schema::table('jobs_invites', function(Blueprint $table) {
			$table->dropForeign('jobs_invites_job_id_foreign');
		});
		Schema::table('jobs_invites', function(Blueprint $table) {
			$table->dropForeign('jobs_invites_company_id_foreign');
		});
		Schema::table('jobs_invites', function(Blueprint $table) {
			$table->dropForeign('jobs_invites_professional_id_foreign');
		});
		Schema::table('subscriptions', function(Blueprint $table) {
			$table->dropForeign('subscriptions_person_id_foreign');
		});
		Schema::table('subscriptions', function(Blueprint $table) {
			$table->dropForeign('subscriptions_payment_id_foreign');
		});
		Schema::table('subscriptions', function(Blueprint $table) {
			$table->dropForeign('subscriptions_plan_id_foreign');
		});
		Schema::table('payments', function(Blueprint $table) {
			$table->dropForeign('payments_order_id_foreign');
		});
		Schema::table('payments', function(Blueprint $table) {
			$table->dropForeign('payments_person_id_foreign');
		});
		Schema::table('orders', function(Blueprint $table) {
			$table->dropForeign('orders_person_id_foreign');
		});
		Schema::table('orders', function(Blueprint $table) {
			$table->dropForeign('orders_plan_id_foreign');
		});
		Schema::table('curriculums', function(Blueprint $table) {
			$table->dropForeign('curriculums_cprofes_id_foreign');
		});
		Schema::table('curriculums', function(Blueprint $table) {
			$table->dropForeign('curriculums_clengua_id_foreign');
		});
		Schema::table('languages', function(Blueprint $table) {
			$table->dropForeign('languages_lacurriculum_id_foreign');
		});
		Schema::table('languages', function(Blueprint $table) {
			$table->dropForeign('languages_lalangue_id_foreign');
		});
		Schema::table('languages', function(Blueprint $table) {
			$table->dropForeign('languages_laspeaking_level_foreign');
		});
		Schema::table('languages', function(Blueprint $table) {
			$table->dropForeign('languages_lalistening_level_foreign');
		});
		Schema::table('languages', function(Blueprint $table) {
			$table->dropForeign('languages_lawriting_level_foreign');
		});
		Schema::table('languages', function(Blueprint $table) {
			$table->dropForeign('languages_lareading_level_foreign');
		});
		Schema::table('skills', function(Blueprint $table) {
			$table->dropForeign('skills_skcurriculum_id_foreign');
		});
		Schema::table('skills', function(Blueprint $table) {
			$table->dropForeign('skills_skill_name_foreign');
		});
		Schema::table('skills', function(Blueprint $table) {
			$table->dropForeign('skills_skproficiency_level_foreign');
		});
		Schema::table('educations', function(Blueprint $table) {
			$table->dropForeign('educations_edcurriculum_id_foreign');
		});
		Schema::table('educations', function(Blueprint $table) {
			$table->dropForeign('educations_degree_type_foreign');
		});
		Schema::table('educations', function(Blueprint $table) {
			$table->dropForeign('educations_edfield_of_study_foreign');
		});
		Schema::table('references', function(Blueprint $table) {
			$table->dropForeign('references_refcurriculum_id_foreign');
		});
		Schema::table('certifications', function(Blueprint $table) {
			$table->dropForeign('certifications_cercurriculum_id_foreign');
		});
		Schema::table('experiences', function(Blueprint $table) {
			$table->dropForeign('experiences_excurriculum_id_foreign');
		});
		Schema::table('presentations', function(Blueprint $table) {
			$table->dropForeign('presentations_precurriculum_id_foreign');
		});
		Schema::table('links', function(Blueprint $table) {
			$table->dropForeign('links_curriculum_id_foreign');
		});
		Schema::table('countries', function(Blueprint $table) {
			$table->dropForeign('countries_curriculum_id_foreign');
		});
		Schema::table('countries', function(Blueprint $table) {
			$table->dropForeign('countries_country_name_foreign');
		});
		Schema::table('liststates', function(Blueprint $table) {
			$table->dropForeign('liststates_lstates_parent_id_foreign');
		});
		Schema::table('liststates', function(Blueprint $table) {
			$table->dropForeign('liststates_lstacountry_id_foreign');
		});
		Schema::table('listcities', function(Blueprint $table) {
			$table->dropForeign('listcities_lcitstates_id_foreign');
		});
		Schema::table('dataperson', function(Blueprint $table) {
			$table->dropForeign('dataperson_dpprofes_id_foreign');
		});
		Schema::table('dataperson', function(Blueprint $table) {
			$table->dropForeign('dataperson_dpgender_foreign');
		});
		Schema::table('dataperson', function(Blueprint $table) {
			$table->dropForeign('dataperson_dpcity_id_foreign');
		});
		Schema::table('dataperson', function(Blueprint $table) {
			$table->dropForeign('dataperson_dpstate_id_foreign');
		});
		Schema::table('dataperson', function(Blueprint $table) {
			$table->dropForeign('dataperson_dpcountry_id_foreign');
		});
		Schema::table('visas', function(Blueprint $table) {
			$table->dropForeign('visas_vicurriculum_id_foreign');
		});
		Schema::table('visas', function(Blueprint $table) {
			$table->dropForeign('visas_vicountry_id_foreign');
		});
		Schema::table('visas', function(Blueprint $table) {
			$table->dropForeign('visas_visa_type_foreign');
		});
		Schema::table('profiles', function(Blueprint $table) {
			$table->dropForeign('profiles_person_id_foreign');
		});
		Schema::table('listprofessions', function(Blueprint $table) {
			$table->dropForeign('listprofessions_parent_profession_id_foreign');
		});
		Schema::table('listprofessions', function(Blueprint $table) {
			$table->dropForeign('listprofessions_person_id_foreign');
		});
		Schema::table('professionals_professions', function(Blueprint $table) {
			$table->dropForeign('professionals_professions_lprofession_id_foreign');
		});
		Schema::table('professionals_professions', function(Blueprint $table) {
			$table->dropForeign('professionals_professions_professional_id_foreign');
		});
		Schema::table('companies_admins', function(Blueprint $table) {
			$table->dropForeign('companies_admins_company_id_foreign');
		});
		Schema::table('companies_admins', function(Blueprint $table) {
			$table->dropForeign('companies_admins_person_id_foreign');
		});
		Schema::table('professionals_job_modalities', function(Blueprint $table) {
			$table->dropForeign('professionals_job_modalities_professional_id_foreign');
		});
		Schema::table('professionals_job_modalities', function(Blueprint $table) {
			$table->dropForeign('professionals_job_modalities_job_modality_id_foreign');
		});
	}
}