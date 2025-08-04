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
        Schema::create('libraries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('author')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('file_id')->nullable()->constrained('files')->onDelete('set null');
            $table->string('isbn')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->after('is_visible');
            $table->foreignId('institute_id')->nullable()->constrained('institutes')->onDelete('set null')->after('user_id');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libraries');
    }
};
