<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReplaceEnumColumnsToIntInTeamConnections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('team_connections', function (Blueprint $table) {
        //     //
        //     $table->dropColumn('connection_status');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('int_in_team_connections', function (Blueprint $table) {
        //     //
        //     $table->enum('connection_status', ['0', '1', '2'])->default(0)->comment('0=pending,1=accepted,2=rejected');
        // });
    }
}
