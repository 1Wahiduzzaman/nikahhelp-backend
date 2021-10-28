<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeRespondedByNullableInTeamConnection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_connections', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('responded_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team_connections', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('responded_by')->nullable()->change();
        });
    }
}
