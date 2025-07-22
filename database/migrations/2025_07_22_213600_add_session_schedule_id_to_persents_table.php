<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionScheduleIdToPersentsTable extends Migration
{
    public function up()
    {
        Schema::table('persents', function (Blueprint $table) {
            $table->unsignedBigInteger('session_schedule_id')->after('id');
            $table->foreign('session_schedule_id')
                  ->references('id')
                  ->on('session_schedules')
                  ->onDelete('cascade');
            $table->unique(['date','session_schedule_id']);
        });
    }

    public function down()
    {
        Schema::table('persents', function (Blueprint $table) {
            $table->dropUnique(['persents_date_session_schedule_id_unique']);
            $table->dropForeign(['session_schedule_id']);
            $table->dropColumn('session_schedule_id');
        });
    }
}
