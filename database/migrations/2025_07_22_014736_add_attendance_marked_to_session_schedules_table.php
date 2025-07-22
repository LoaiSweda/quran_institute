<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('session_schedules', function (Blueprint $table) {
            $table->boolean('attendance_marked')->default(false);
        });
    }

    public function down()
    {
        Schema::table('session_schedules', function (Blueprint $table) {
            $table->dropColumn('attendance_marked');
        });
    }
};
