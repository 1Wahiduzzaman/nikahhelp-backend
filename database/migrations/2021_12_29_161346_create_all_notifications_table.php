<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('all_notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('team_id')->nullable();
            $table->bigInteger('sender')->nullable();
            $table->bigInteger('receiver')->nullable();
            $table->string('title')->nullable();   
            $table->text('description')->nullable();   
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
        Schema::dropIfExists('all_notifications');
    }
}
//raz fix