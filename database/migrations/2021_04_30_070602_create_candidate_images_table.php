<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('image_type')->comment('[1=>avatar,2=>Main image, [3,8] => Additional Image[3,8]]');
            $table->string('image_path');
            $table->tinyInteger('image_visibility')->default(2)->comment('[1=>only me,2=>My team, 3=>connected team, 4 => everyone]');
            $table->string('disk', 50)->default('local');
            $table->timestamps();

            $table->foreign('user_id', 'candidate_images_user_id')->references('id')->on('users');
            //            $table->unique(['user_id','image_type'],'candidate_images_unique_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidate_images');
    }
}
