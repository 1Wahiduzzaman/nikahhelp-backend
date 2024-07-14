<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('team_id',50);
            $table->string('name',255);
            $table->string('description',255)->nullable();
            $table->tinyInteger('member_count')->default(0);
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->date('subscription_expire_at')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('password',80);
            $table->string('logo',255)->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teams');
    }
}
