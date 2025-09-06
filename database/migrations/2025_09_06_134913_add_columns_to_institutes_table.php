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
        Schema::table('institutes', function (Blueprint $table) {
            if (!Schema::hasColumn('institutes', 'institute_stamp_path')) {
                $table->string('institute_stamp_path')->nullable(); // institute stamp image
            }
            if (!Schema::hasColumn('institutes', 'director_signature_path')) {
                $table->string('director_signature_path')->nullable()->after('institute_stamp_path'); // director signature image
            }
            if (!Schema::hasColumn('institutes', 'phone')) {
                $table->string('phone')->nullable()->after('director_signature_path');
            }
            if (!Schema::hasColumn('institutes', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institutes', function (Blueprint $table) {
            //
        });
    }
};
