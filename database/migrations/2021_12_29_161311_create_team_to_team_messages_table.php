<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamToTeamMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_to_team_messages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('from_team_id')->nullable();
            $table->bigInteger('to_team_id')->nullable();
            $table->bigInteger('team_private_chat_id')->nullable();
            $table->bigInteger('sender')->nullable();                     
            $table->longText('body')->nullable();   
            $table->string('attachment')->nullable();  
            $table->tinyInteger('seen')->default(0); 
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
        Schema::dropIfExists('team_to_team_messages');
    }
}
