<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_chats', function (Blueprint $table) {
            DB::statement("CREATE TABLE `team_chats` ( `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, `from_team_id` bigint(20) DEFAULT NULL, `to_team_id` bigint(20) DEFAULT NULL, `sender` bigint(20) DEFAULT NULL, `receiver` bigint(20) DEFAULT NULL, `created_at` datetime DEFAULT NULL, `updated_at` datetime DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_chats');
    }
}