<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscriptionIdToTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (! Schema::hasColumn('teams', 'subscription_id')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->unsignedBigInteger('subscription_id')->nullable()->after('member_count');
            });
        }
        if (! Schema::hasColumn('teams', 'subscription_expire_at')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->date('subscription_expire_at')->nullable()->after('subscription_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('teams', 'subscription_id')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->dropColumn('subscription_id');
            });
        }
        if (Schema::hasColumn('teams', 'subscription_expire_at')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->dropColumn('subscription_expire_at');
            });
        }
    }
}
