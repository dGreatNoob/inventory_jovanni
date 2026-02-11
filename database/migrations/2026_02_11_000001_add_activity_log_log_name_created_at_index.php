<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActivityLogLogNameCreatedAtIndex extends Migration
{
    public function up()
    {
        $connection = Schema::connection(config('activitylog.database_connection'));
        $tableName = config('activitylog.table_name');

        if (!$connection->hasTable($tableName)) {
            return;
        }

        $connection->table($tableName, function (Blueprint $table) {
            $table->index(['log_name', 'created_at'], 'activity_log_log_name_created_at_index');
        });
    }

    public function down()
    {
        $connection = Schema::connection(config('activitylog.database_connection'));
        $tableName = config('activitylog.table_name');

        if (!$connection->hasTable($tableName)) {
            return;
        }

        $connection->table($tableName, function (Blueprint $table) {
            $table->dropIndex('activity_log_log_name_created_at_index');
        });
    }
}
