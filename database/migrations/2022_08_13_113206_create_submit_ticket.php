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
            $table->json('user');
            $table->string('issue_type');
            $table->string('issue');
            $table->bigInteger('screen_shot_id');
            $table->string('screen_shot_path');
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
