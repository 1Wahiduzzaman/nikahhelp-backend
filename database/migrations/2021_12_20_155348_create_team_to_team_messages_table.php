<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamToTeamMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_to_team_messages', function (Blueprint $table) {
            DB::statement("CREATE TABLE `team_to_team_messages` ( `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, `team_chat_id` bigint(20) DEFAULT NULL, `from_team_id` bigint(20) DEFAULT NULL, `to_team_id` bigint(20) DEFAULT NULL, `sender` bigint(20) DEFAULT NULL, `body` longtext COLLATE utf8_unicode_ci, `attachment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `seen` tinyint(1) DEFAULT '0', `created_at` datetime DEFAULT NULL, `updated_at` datetime DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_to_team_messages');
    }
}
