<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RepresentativeInformationsDataInputStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('representative_informations', function ($table) {
            $table->integer('data_input_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('representative_informations', function ($table) {
            $table->dropColumn('data_input_status');
        });
    }
}
