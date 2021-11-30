<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateInformationTable extends Migration
{
    public function up()
    {
        Schema::create('candidate_information', function (Blueprint $table) {

            //Basic Info
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('screen_name', 100)->nullable();
            $table->date('dob')->nullable();
            $table->string('mobile_number', 15)->nullable();
            $table->string('mobile_country_code', 15)->nullable()->default('BD');
//            $table->string('per_telephone_no', 15)->nullable();
            $table->tinyInteger('per_gender')->comment('1=Male,2=Female,3=Others,4=Do not disclose')->nullable();

            //Personal information.
            $table->float('per_height')->nullable();
            $table->string('per_employment_status')->nullable();
            $table->unsignedBigInteger('per_education_level_id')->nullable();
            $table->unsignedBigInteger('per_religion_id')->nullable();
            $table->string('per_occupation')->nullable();
            $table->string('per_ethnicity')->nullable();
            $table->string('per_mother_tongue', 100)->nullable();
            $table->string('per_health_condition', 255)->nullable();
            $table->unsignedBigInteger('per_nationality')->nullable();
            $table->unsignedBigInteger('per_country_of_birth')->nullable();

            //Personal information.
            $table->string('per_email', 100)->nullable();
            $table->string('per_current_residence_country')->nullable();
            $table->string('per_current_residence_city')->nullable();
            $table->string('per_permanent_country')->nullable();
            $table->string('per_permanent_city')->nullable();
            $table->string('per_county')->nullable();
            $table->string('per_telephone_no', 15)->nullable();
//            $table->string('mobile_number', 15)->nullable();
//            $table->string('mobile_country_code', 15)->nullable()->default('BD');
            $table->string('per_permanent_post_code')->nullable();
            $table->string('per_permanent_address')->nullable();

//            $table->unsignedBigInteger('per_current_residence')->nullable();
//            $table->string('per_address')->nullable();
            $table->enum('per_marital_status',['single','married','divorced','divorced_with_children','separated','widowed','others'])->default('single');
            $table->boolean('per_have_children')->nullable()->comment('0=no,1=yes');
            $table->string('per_children')->nullable()->comment('Json value for children');
            $table->string('per_currently_living_with')->nullable();
            $table->enum('per_willing_to_relocate',[1,2,3,4])->nullable();
            $table->boolean('per_smoker')->nullable()->comment('0=No,1=Yes');
            $table->string('per_language_speak')->nullable();
            $table->string('per_hobbies_interests')->nullable();
            $table->string('per_food_cuisine_like')->nullable();
            $table->string('per_things_enjoy')->nullable();
            $table->string('per_thankfull_for')->nullable();
            $table->string('per_about')->nullable();


            // Candidate Preference
            $table->tinyInteger('pre_partner_age_min')->nullable();
            $table->tinyInteger('pre_partner_age_max')->nullable();
            $table->float('pre_height_min', 4, 2)->nullable();
            $table->float('pre_height_max', 4, 2)->nullable();
            $table->boolean('pre_has_country_allow_preference')->default(0)->nullable();
            $table->boolean('pre_has_country_disallow_preference')->default(0)->nullable();
            //$table->string('pre_countries'); See candidate_country_user
            //$table->string('pre_cities'); See candidate_city
            $table->string('pre_partner_religions',255)->nullable();
            $table->string('pre_ethnicities',255)->nullable();
            //$table->string('pre_nationality',)->nullable(); See candidate_nationality_user
            $table->unsignedBigInteger('pre_study_level_id')->nullable();
            $table->string('pre_employment_status')->nullable();
            $table->string('pre_occupation')->nullable();
            $table->boolean('pre_preferred_divorcee')->default(false);
            $table->boolean('pre_preferred_divorcee_child')->default(false)->comment('divorcee with child');
            $table->string('pre_other_preference',255)->nullable();
            $table->string('pre_description',255)->nullable();
            $table->tinyInteger('pre_pros_part_status')->nullable()->comment('1=Initial phase, 2= partially complicated, 3= completed');

            $table->tinyInteger('pre_strength_of_character_rate')->nullable();
            $table->tinyInteger('pre_look_and_appearance_rate')->nullable();
            $table->tinyInteger('pre_religiosity_or_faith_rate')->nullable();
            $table->tinyInteger('pre_manners_socialskill_ethics_rate')->nullable();
            $table->tinyInteger('pre_emotional_maturity_rate')->nullable();
            $table->tinyInteger('pre_good_listener_rate')->nullable();
            $table->tinyInteger('pre_good_talker_rate')->nullable();
            $table->tinyInteger('pre_wiling_to_learn_rate')->nullable();
            $table->tinyInteger('pre_family_social_status_rate')->nullable();
            $table->tinyInteger('pre_employment_wealth_rate')->nullable();
            $table->tinyInteger('pre_education_rate')->nullable();

            $table->tinyInteger('pre_things_important_status')->nullable()->comment('1=Initial phase, 2= partially complicated, 3= completed');           // Family Information
            $table->string('fi_father_name')->nullable();
            $table->string('fi_father_profession')->nullable();
            $table->string('fi_mother_name')->nullable();
            $table->string('fi_mother_profession')->nullable();
            $table->string('fi_siblings_desc')->comment('Siblings descriptions')->nullable();
            $table->string('fi_country_of_origin')->nullable();
            $table->string('fi_family_info')->nullable();
            $table->tinyInteger('anybody_can_see')->comment('0=No,1=Yes')->default(0);
            $table->tinyInteger('only_team_can_see')->comment('0=No,1=Yes')->default(0);
            $table->tinyInteger('team_connection_can_see')->comment('0=No,1=Yes')->default(0);


            // Verification
            $table->unsignedBigInteger('ver_country_id')->nullable();
            $table->unsignedBigInteger('ver_city_id')->nullable();
            $table->string('ver_document_type')->nullable();
            $table->text('ver_image_front')->nullable();
            $table->text('ver_image_back')->nullable();
            $table->string('ver_recommences_title')->nullable();
            $table->string('ver_recommences_first_name')->nullable();
            $table->string('ver_recommences_last_name')->nullable();
            $table->string('ver_recommences_occupation')->nullable();
            $table->string('ver_recommences_address')->nullable();
            $table->string('ver_recommences_mobile_no')->nullable();
            $table->tinyInteger('ver_status')->default(0)->comment('0 for not verified 1 for verified');


            // Image upload
            $table->string('per_avatar_url', 2083)->nullable();
            $table->string('per_main_image_url', 2083)->nullable();
            $table->tinyInteger('is_publish')->default(0);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('per_religion_id')->references('id')->on('religions');
//            $table->foreign('pre_partner_religion_id')->references('id')->on('religions');
            $table->foreign('per_education_level_id')->references('id')->on('study_level');
            $table->foreign('pre_study_level_id')->references('id')->on('study_level');
        });
    }

    public function down()
    {
        Schema::dropIfExists('candidate_information');
    }
}
