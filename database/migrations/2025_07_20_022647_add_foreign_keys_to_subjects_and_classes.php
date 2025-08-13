<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // subjects → institutes
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreign('institute_id')
                  ->references('id')->on('institutes')
                  ->onDelete('cascade');
        });

        // classes → subjects & classes → users
        Schema::table('classes', function (Blueprint $table) {
            $table->foreign('subject_id')
                  ->references('id')->on('subjects')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['institute_id']);
        });
    }
};
