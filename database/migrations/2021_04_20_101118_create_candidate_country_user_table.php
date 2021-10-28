<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateCountryUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_country_user', function (Blueprint $table) {
            $table->bigInteger('user_id'); // user id
            $table->bigInteger('candidate_pre_country_id'); // Countries id
            $table->bigInteger('candidate_pre_city_id')->nullable(true); // Countries id
            $table->boolean('allow')->default(1)->comment('1=allowed,0=disallowed');

            $table->unique(['user_id','candidate_pre_country_id','candidate_pre_city_id','allow'],'candidate_country_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidate_country_user');
    }
}
