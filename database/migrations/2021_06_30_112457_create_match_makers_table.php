<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchMakersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_makers', function (Blueprint $table) {
            $table->increments('id');
            //Basic Info
            $table->unsignedBigInteger('user_id');
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('screen_name', 100)->nullable();

            // Essential Information
            $table->tinyInteger('per_gender')->comment('1=Male,2=Female,3=Others,4=Do not disclose')->nullable();
            $table->date('dob')->nullable();
            $table->string('per_occupation')->nullable();

            //Personal information.
            $table->string('per_email', 100)->nullable();
            $table->string('per_current_residence_country')->nullable();
            $table->string('per_current_residence_city')->nullable();
            $table->string('per_permanent_country')->nullable();
            $table->string('per_permanent_city')->nullable();
            $table->string('per_county')->nullable();
            $table->string('per_telephone_no', 15)->nullable();
            $table->string('mobile_number', 15)->nullable();
            $table->string('mobile_country_code', 15)->nullable()->default('BD');
            $table->string('per_permanent_post_code')->nullable();
            $table->string('per_permanent_address')->nullable();

            //verification information
            $table->tinyInteger('is_document_upload')->comment('0=No,1=Yes')->nullable();
            $table->string('ver_country')->nullable();
            $table->string('ver_city')->nullable();
            $table->string('ver_document_type')->nullable();
            $table->string('ver_document_frontside')->nullable();
            $table->string('ver_document_backside')->nullable();
            $table->string('ver_recommender_title')->nullable();
            $table->string('ver_recommender_first_name')->nullable();
            $table->string('ver_recommender_last_name')->nullable();
            $table->string('ver_recommender_occupation')->nullable();
            $table->string('ver_recommender_address')->nullable();
            $table->string('ver_recommender_mobile_no')->nullable();


          // Business information
            $table->string('capacity', 100)->nullable();
            $table->string('company_or_other', 255)->nullable();
            $table->string('occupation', 70)->nullable();
            $table->string('match_maker_duration', 100)->nullable();
            $table->string('match_qt', 10)->nullable();
            $table->string('last_six_month_match_qt', 10)->nullable();
            $table->string('match_per_county')->nullable();
            $table->string('match_community')->nullable();
            $table->string('have_previous_experience',10)->nullable();
            $table->string('previous_experience',255)->nullable();
            $table->string('can_share_last_three_match',10)->nullable();
            $table->string('match_one')->nullable();
            $table->string('match_two')->nullable();
            $table->string('match_three')->nullable();
            // Image upload
            $table->string('per_avatar_url', 2083)->nullable();
            $table->string('per_main_image_url', 2083)->nullable();
            $table->tinyInteger('anybody_can_see')->comment('0=No,1=Yes')->default(0);
            $table->tinyInteger('only_team_can_see')->comment('0=No,1=Yes')->default(0);
            $table->tinyInteger('team_connection_can_see')->comment('0=No,1=Yes')->default(0);
            $table->tinyInteger('is_agree')->comment('0=No,1=Yes')->default(0);
            $table->integer('data_input_status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('match_makers');
    }
}
