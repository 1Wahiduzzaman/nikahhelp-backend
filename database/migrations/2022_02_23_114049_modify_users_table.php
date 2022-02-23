<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModifyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //  ALTER TABLE `users` MODIFY COLUMN `status`  enum('1','2','3','4','5','6','7','8','9','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' AFTER `password`;
        Schema::table('branch_incharges', function (Blueprint $table) {            
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `status`  enum('1','2','3','4','5','6','7','8','9','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' AFTER `password`");
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
