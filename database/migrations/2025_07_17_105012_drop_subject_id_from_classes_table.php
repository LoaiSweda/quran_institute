<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('classes', function (Blueprint $table) {
            // إذا فعلاً يوجد FK مسماة conventions (classes_subject_id_foreign):
            $table->dropForeign(['subject_id']);
            // ثمّ نحذف العمود
            $table->dropColumn('subject_id');
        });
    }

    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            // لإرجاع التغييرات إذا رجعنا المِيغرايشن:
            $table->foreignId('subject_id')
                ->after('id')
                ->constrained('subjects')
                ->onDelete('cascade');
        });
    }
};
