<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationAndUserTypeColumnsInTeamMemberInvitations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_member_invitations', function (Blueprint $table) {
            //
            $table->string('user_type',255)->nullable();
            $table->string('relationship',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team_member_invitations', function (Blueprint $table) {
            //
            $table->dropColumn('user_type');
            $table->dropColumn('relationship');
        });
    }
}
