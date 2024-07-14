<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamPrivateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_private_chats', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('from_team_id')->nullable();
            $table->bigInteger('to_team_id')->nullable();
            $table->bigInteger('sender')->nullable();
            $table->bigInteger('receiver')->nullable(); 
            $table->tinyInteger('is_friend')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_private_chats');
    }
}
