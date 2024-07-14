<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePreHeightMinToCandidateInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidate_information', function (Blueprint $table) {
            $table->string('pre_height_min', 20)->change()->nullable();
            $table->string('pre_height_max', 20)->change()->nullable();
            $table->string('pre_partner_religions', 255)->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
