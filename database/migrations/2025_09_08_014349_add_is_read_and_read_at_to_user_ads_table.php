<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('user_ads', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('watches_role');
            $table->timestamp('read_at')->nullable()->after('is_read');
        });
    }
    public function down(): void {
        Schema::table('user_ads', function (Blueprint $table) {
            $table->dropColumn(['is_read','read_at']);
        });
    }
};
