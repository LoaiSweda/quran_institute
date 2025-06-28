<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // نجيب دور student
        $role = Role::firstWhere('name', 'student');
        if (! $role) {
            $this->command->error("دور student غير موجود!");
            return;
        }

        // نجيب كل الأوصياء حتى نربط كل طالب بوصي
        $guardians = Guardian::all();
        if ($guardians->isEmpty()) {
            $this->command->error("لا يوجد أوصياء. شغّل أولاً GuardianSeeder!");
            return;
        }

        // مصفوفة بيانات طلاب افتراضيين
        $students = [
            [
                'first_name'         => 'عمر',
                'last_name'          => 'الصياد',
                'phone'              => '0500000001',
                'address'            => 'الدمام',
                'birthdate'          => now()->subYears(12)->toDateString(),
                'father_name'        => 'سعيد القرشي',
                'points'             => 0,
                'present_percentage' => 100,
                'email'              => 'ali.student@quran-institute.local',
                'password'           => '123123123',
            ],
            [
                'first_name'         => 'لؤي',
                'last_name'          => 'سويده',
                'phone'              => '0500000002',
                'address'            => 'مكة المكرمة',
                'birthdate'          => now()->subYears(10)->toDateString(),
                'father_name'        => 'حسن السعيد',
                'points'             => 0,
                'present_percentage' => 95,
                'email'              => 'fatima.student@quran-institute.local',
                'password'           => '123123123',
            ],
        ];

        foreach ($students as $s) {
            // ربط الطالب بوصي عشوائي
            $guardian = $guardians->random();

            // إنشاء أو تحديث المستخدم
            $user = User::updateOrCreate(
                ['email' => $s['email']],
                [
                    'password' => Hash::make($s['password']),
                    'role_id'  => $role->id,
                ]
            );

            // إنشاء حقل QR عشوائي
            $qr = Str::upper(Str::random(10));

            // إنشاء أو تحديث بيانات الطالب
            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name'         => $s['first_name'],
                    'last_name'          => $s['last_name'],
                    'guardian_id'        => $guardian->id,
                    'qr'                 => $qr,
                    'phone'              => $s['phone'],
                    'address'            => $s['address'],
                    'birthdate'          => $s['birthdate'],
                    'father_name'        => $s['father_name'],
                    'points'             => $s['points'],
                    'present_percentage' => $s['present_percentage'],
                ]
            );

            $this->command->info("تم إنشاء/تحديث Student: {$s['email']} (QR: $qr)");
        }
    }
}
