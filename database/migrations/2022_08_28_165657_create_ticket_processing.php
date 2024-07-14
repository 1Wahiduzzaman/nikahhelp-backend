<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketProcessing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_processing', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ticket_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->longText('message')->nullable();
            $table->bigInteger('status')->nullable();
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
        Schema::dropIfExists('ticket_processing');
    }
}
