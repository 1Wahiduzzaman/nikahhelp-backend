<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamListedCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_listed_candidates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id'); // user id
            $table->bigInteger('team_listed_by')->nullable(false); // user id
            $table->bigInteger('team_listed_for')->nullable(true)->comment('team_id'); // team
            $table->date('team_listed_date')->nullable(true); // date
            $table->tinyInteger('is_block')->default(0); // date
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
        Schema::dropIfExists('team_listed_candidates');
    }
}
