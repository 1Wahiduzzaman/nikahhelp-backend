<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            DB::statement("CREATE TABLE `messages` ( `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, `team_id` bigint(20) DEFAULT NULL, `chat_id` bigint(20) DEFAULT NULL, `sender` bigint(20) DEFAULT NULL, `receiver` bigint(20) DEFAULT NULL, `body` text COLLATE utf8_unicode_ci, `attachment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `seen` tinyint(1) DEFAULT '0', `created_at` datetime DEFAULT NULL, `updated_at` datetime DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
