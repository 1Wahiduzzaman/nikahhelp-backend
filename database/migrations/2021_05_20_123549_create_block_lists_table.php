<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->nullable(false); // user id
            $table->bigInteger('block_by')->nullable(false); // user id
            $table->bigInteger('block_for')->nullable(true); // team
            $table->string('type', 20)->default('single')->comment('single or team');
            $table->date('block_date')->nullable(true); // date
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('block_lists');
    }
}
