<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmitTicket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submit_ticket', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->json('user')->nullable();
            $table->string('issue_type')->nullable();
            $table->string('issue')->nullable();
            $table->bigInteger('screen_shot_id')->nullable();
            $table->string('screen_shot_path')->nullable();
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
        Schema::dropIfExists('submit_ticket');
    }
}
