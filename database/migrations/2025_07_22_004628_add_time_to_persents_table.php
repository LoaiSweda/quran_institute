<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('persents', function (Blueprint $table) {
            $table->time('time')->nullable()->after('date');
        });

        DB::statement('UPDATE persents SET time = "00:00:00"');
        
        Schema::table('persents', function (Blueprint $table) {
            $table->time('time')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('persents', function (Blueprint $table) {
            $table->dropColumn('time');
        });
    }
};
