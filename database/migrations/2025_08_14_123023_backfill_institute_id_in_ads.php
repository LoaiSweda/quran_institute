<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // نعالج الصفوف التي لا تملك institute_id فقط
        DB::table('ads')
            ->whereNull('institute_id')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $instituteId = null;

                    // 1) إن كان ناشر الإعلان مديرًا لمعهد (institutes.user_id = user_id)
                    $instByManager = DB::table('institutes')
                        ->where('user_id', $row->user_id)
                        ->value('id');

                    if ($instByManager) {
                        $instituteId = $instByManager;
                    } else {
                        // 2) ابحث عن أول معهد مرتبط بالمستخدم عبر pivot (institute_user)
                        $instByPivot = DB::table('institute_user')
                            ->where('user_id', $row->user_id)
                            ->orderBy('created_at')
                            ->value('institute_id');

                        if ($instByPivot) {
                            $instituteId = $instByPivot;
                        }
                    }

                    // لو لم نعثر على معهد، نتركه NULL (وسنجعله NOT NULL لاحقًا فقط إذا اكتملت التعبئة)
                    if ($instituteId) {
                        DB::table('ads')->where('id', $row->id)->update([
                            'institute_id' => $instituteId,
                        ]);
                    }
                }
            });
    }

    public function down(): void
    {
        // لا شيء — هذه عملية تعبئة ولا نريد عكسها تلقائيًا
    }
};
