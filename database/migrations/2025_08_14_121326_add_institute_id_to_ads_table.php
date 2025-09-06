<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->foreignId('institute_id')
                ->nullable()
                ->after('user_id')
                ->constrained('institutes')
                ->cascadeOnDelete(); 
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institute_id');
        });
    }
};
