<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortListedCandidatesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('short_listed_candidates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id'); // user id
            $table->bigInteger('shortlisted_by')->nullable(false); // user id
            $table->bigInteger('shortlisted_for')->nullable(true); // team
            $table->date('shortlisted_date')->nullable(true); // date
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
        Schema::drop('short_listed_candidates');
    }
}
