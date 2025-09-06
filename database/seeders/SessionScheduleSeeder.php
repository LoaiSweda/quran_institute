<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SessionSchedule;
use App\Models\EducationClass;

class SessionScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherId = 2;

        $classIds = EducationClass::pluck('id')->toArray();

        if (empty($classIds)) {
            $this->command->info('❌ لا توجد صفوف في جدول classes. يرجى تشغيل EducationClassSeeder أولاً.');
            return;
        }

        foreach (range(0, 5) as $dayOfWeek) {
            SessionSchedule::create([
                'user_id'     => $teacherId,
                'class_id'    => $classIds[array_rand($classIds)],
                'day_of_week' => $dayOfWeek,
                'start_time'  => '09:00:00',
                'end_time'    => '10:00:00',
            ]);
        }

        $this->command->info('✅ تم إنشاء بيانات SessionScheduleSeeder بنجاح.');
    }
}
