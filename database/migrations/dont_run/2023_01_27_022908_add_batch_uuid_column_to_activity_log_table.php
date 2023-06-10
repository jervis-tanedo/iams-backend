<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class AddBatchUuidColumnToActivityLogTable extends Migration
{
    public function up()
    {
        Schema::connection(config('activitylog.database_connection'))->table(config('activitylog.table_name'), function (Blueprint $table) {
            $table->uuid('batch_uuid')->nullable()->after('properties');
            // $table->uuid('subject_id')->nullable()->after('subject_key');
            // $table->uuid('subject_id')->change();
            // DB::statement('ALTER TABLE activity_log ALTER subject_id TYPE UUID');
            // DB::statement('ALTER TABLE activity_log ALTER COLUMN 
            //       subject_id TYPE UUID SET USING UUID USING (uuid_generate_v4())');
            DB::statement('ALTER TABLE activity_log ALTER COLUMN subject_id TYPE character');
            DB::statement('ALTER TABLE activity_log ALTER COLUMN subject_id TYPE UUID USING subject_id::uuid');
        });
    }

    public function down()
    {
        Schema::connection(config('activitylog.database_connection'))->table(config('activitylog.table_name'), function (Blueprint $table) {
            $table->dropColumn('batch_uuid');
            // $table->dropIndex(['subject_id']);
            // $table->dropColumn('subject_id');
        });
    }
}
