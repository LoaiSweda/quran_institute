<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('institutes', function (Blueprint $table) {
            // نُسقط القيد الأجنبي الحالي إذا وُجد
            $table->dropForeign(['user_id']);
            // نجعل العمود قابلًا لأن يكون null
            $table->unsignedBigInteger('user_id')->nullable()->change();
            // نُعيد إضافة القيد بحيث إذا حُذف المستخدم يُصبح user_id = NULL تلقائيًا
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('institutes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            // هنا ترجع القيد الأصلي، مثلاً منع الحذف أو cascade حسب ما كان سابقًا
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
