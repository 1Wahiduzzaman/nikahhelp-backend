<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connected_team_last_seens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_chat_id');
            $table->unsignedBigInteger('last_seen_msg_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('team_chat_id')->references('id')->on('team_chats')->onDelete('cascade');
            $table->foreign('last_seen_msg_id')->references('id')->on('team_to_team_messages')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('connected_team_last_seen');
    }
};
