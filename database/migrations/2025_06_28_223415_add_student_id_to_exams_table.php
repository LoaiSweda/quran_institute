<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentIdToExamsTable extends Migration
{
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            // نضيف العمود فقط إذا لم يكن موجوداً
            if (! Schema::hasColumn('exams', 'student_id')) {
                $table->foreignId('student_id')
                      ->after('class_id')
                      ->constrained('students')
                      ->onDelete('cascade');
            }
        });
    }
    
    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('student_id');
        });
    }
}
