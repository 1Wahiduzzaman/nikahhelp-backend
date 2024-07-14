<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsdocuploadedRepinfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('representative_informations', function (Blueprint $table) {
            $table->tinyInteger('is_uplaoded_doc')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('representative_informations', function (Blueprint $table) {
            $table->dropColumn('is_uplaoded_doc');
        });
    }
}
