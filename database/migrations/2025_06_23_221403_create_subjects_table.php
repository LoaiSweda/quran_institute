<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();

            // نحضّر العمود فقط بدون FK
            $table->unsignedBigInteger('institute_id');

            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('exams_count')->default(0);
            $table->string('level')->nullable();
            $table->float('degree')->nullable();
            $table->integer('total_sessions')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
