<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\EducationClass;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // 1) دور الطالب ودور المعلم
        $studentRole = Role::where('name', 'student')->first();
        $teacherRole = Role::where('name', 'teacher')->first();

        if (! $studentRole || ! $teacherRole) {
            $this->command->error('❌ تأكد من وجود أدوار student و teacher في جدول roles');
            return;
        }

        // 2) أول مُعلم
        $teacher = User::where('role_id', $teacherRole->id)->first();
        if (! $teacher) {
            $this->command->error('❌ لم أجد أي مستخدم برتبة teacher');
            return;
        }

        // 3) أول صفّين لذلك المُعلم
        $classes = EducationClass::where('user_id', $teacher->id)
                                 ->take(2)
                                 ->get();
        if ($classes->count() < 2) {
            $this->command->error('❌ يجب أن يكون هناك صفّين على الأقل للمعلم (user_id='.$teacher->id.')');
            return;
        }

        // 4) إنشاء 10 طلاب
        for ($i = 1; $i <= 2; $i++) {
            // أ) انشاء المستخدم
            $user = User::create([
                'email'    => "student{$i}@gmail.com",
                'password' => Hash::make('123123123'),
                'role_id'  => $studentRole->id,
            ]);

            // ب) انشاء سجل في جدول students
            Student::create([
                'user_id'            => $user->id,
                'first_name'         => "طالب{$i}",
                'last_name'          => "مثال",
                'guardian_id'        => null,
                'qr'                 => "QR-S{$i}",
                'phone'              => "01000000{$i}",
                'address'            => "عنوان الطالب {$i}",
                'birthdate'          => now()->subYears(15)->toDateString(),
                'father_name'        => "والد{$i}",
                'points'             => 0,
                'present_percentage' => 0,
                'image'              => null,
            ]);

            // ج) ربط الطالب بالصفّين
            foreach ($classes as $class) {
                $class->users()->attach($user->id);
            }
        }

        $this->command->info('✅ تم إنشاء 10 طلاب وربطهم بأول صفّين للمعلّم #'.$teacher->id);
    }
}
