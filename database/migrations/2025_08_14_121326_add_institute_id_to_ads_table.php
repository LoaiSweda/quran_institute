<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            // نضيف العمود Nullable أولًا لكي نتمكن من تعبئته ثم نجعله NOT NULL لاحقًا
            $table->foreignId('institute_id')
                ->nullable()
                ->after('user_id')
                ->constrained('institutes')
                ->cascadeOnDelete(); // عند حذف المعهد احذف الإعلانات التابعة له
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            // لإلغاء المفتاح + حذف العمود
            $table->dropConstrainedForeignId('institute_id');
        });
    }
};
