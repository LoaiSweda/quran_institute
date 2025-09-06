<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('ads')
            ->whereNull('institute_id')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $instituteId = null;

                    $instByManager = DB::table('institutes')
                        ->where('user_id', $row->user_id)
                        ->value('id');

                    if ($instByManager) {
                        $instituteId = $instByManager;
                    } else {
                        $instByPivot = DB::table('institute_user')
                            ->where('user_id', $row->user_id)
                            ->orderBy('created_at')
                            ->value('institute_id');

                        if ($instByPivot) {
                            $instituteId = $instByPivot;
                        }
                    }

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
    }
};
