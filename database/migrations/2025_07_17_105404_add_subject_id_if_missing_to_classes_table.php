<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('classes', function (Blueprint $table) {
            // إذا العمود غير موجود، ضعه مع الـ FK
            if (! Schema::hasColumn('classes', 'subject_id')) {
                $table->foreignId('subject_id')
                    ->after('id')
                    ->constrained('subjects')
                    ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'subject_id')) {
                $table->dropForeign(['subject_id']);
                $table->dropColumn('subject_id');
            }
        });
    }
};
