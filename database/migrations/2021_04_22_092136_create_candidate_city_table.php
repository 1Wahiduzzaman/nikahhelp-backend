<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateCityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_city', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id'); // user id
            $table->unsignedBigInteger('country_id'); // cities id
            $table->unsignedBigInteger('city_id'); // cities id
            $table->boolean('allow')->default(1)->comment('1=allowed,0=disallowed');

            $table->unique(['user_id','country_id','city_id','allow'],'candidate_city_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidate_city');
    }
}
