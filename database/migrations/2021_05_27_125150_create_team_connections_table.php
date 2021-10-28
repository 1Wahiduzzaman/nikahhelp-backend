<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_connections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_team_id');
            $table->unsignedBigInteger('to_team_id');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('responded_by')->nullable();
            $table->enum('connection_status', ['0', '1', '2'])->default(0)->comment('0=pending,1=accepted,2=rejected');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();
            $table->foreign('from_team_id')->references('id')->on('teams');
            $table->foreign('to_team_id')->references('id')->on('teams');
            $table->foreign('requested_by')->references('id')->on('users');
            $table->foreign('responded_by')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_connections');
    }
}
