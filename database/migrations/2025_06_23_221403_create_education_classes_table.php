<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // نحضّر الأعمدة فقط بدون FK
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('user_id');

            $table->integer('students_count')->default(0);
            $table->integer('session_count')->default(0);
            $table->string('qr')->nullable();
            $table->float('present_percentage')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
