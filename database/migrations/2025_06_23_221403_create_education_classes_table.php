<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1) إذا كان الجدول موجودًا، نحذفه بالكامل
        Schema::dropIfExists('classes');

        // 2) نُنشئ الجدول من جديد مع العمود subject_id
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // العمود الجديد مع FK إلى جدول subjects
            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->onDelete('cascade');

            // معرّف المعلم (صاحب الصف)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->integer('students_count')->default(0);
            $table->integer('session_count')->default(0);
            $table->string('qr')->nullable();
            $table->float('present_percentage')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ببساطة نحذف الجدول عندما نرجّع المِيغرايشن
        Schema::dropIfExists('classes');
    }
};
