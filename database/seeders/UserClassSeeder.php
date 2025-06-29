<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\EducationClass;

class UserClassSeeder extends Seeder
{
    public function run(): void
    {
        // 1) جلب دور الطالب
        $studentRole = Role::where('name', 'student')->first();
        if (! $studentRole) {
            $this->command->error('❌ لم يتم العثور على دور "student" في جدول roles.');
            return;
        }

        // 2) جلب جميع الطلاب (user_id)
        $studentIds = User::where('role_id', $studentRole->id)
                          ->pluck('id')
                          ->toArray();
        if (empty($studentIds)) {
            $this->command->info('ℹ️ لا يوجد طلاب لإنشاء الربط.');
            return;
        }

        // 3) جلب أول صفّين حسب الـ ID
        $classes = EducationClass::orderBy('id')
                                 ->take(2)
                                 ->get();
        if ($classes->count() < 2) {
            $this->command->error('❌ لا يوجد صفّين على الأقل في جدول classes.');
            return;
        }

        // 4) ربط كل طالب بكلٍّ من الصفّين (بدون تفريغ الروابط السابقة)
        foreach ($classes as $class) {
            $class->users()->syncWithoutDetaching($studentIds);
            $this->command->info("✅ رُبط " . count($studentIds) . " طالب بالصف: {$class->name} (ID: {$class->id})");
        }
    }
}
