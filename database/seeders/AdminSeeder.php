<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Admin;
use App\Models\Teacher;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // نضم دور المعلم 'teacher' إلى القائمة
        $roles = Role::whereIn('name', [
            'super admin',
            'admin',
            'institute manager',
            'teacher',
        ])->get();

        foreach ($roles as $role) {
            // إنشاء مستخدم لكل دور بكلمة مرور افتراضية
            $user = User::create([
                'email'    => $role->name . '@quran-institute.local',
                'password' => Hash::make('123123123'),
                'role_id'  => $role->id,
            ]);

            // إذا كان الدور من نوع super admin أو admin أو institute manager
            if (in_array($role->name, ['super admin', 'admin', 'institute manager'])) {
                Admin::create([
                    'user_id'    => $user->id,
                    'first_name' => ucwords(str_replace(' ', '_', $role->name)),
                    'last_name'  => 'Quran',
                    'phone'      => '0000000000',
                    'address'    => 'عنوان افتراضي',
                    'birthdate'  => now()->subYears(30)->toDateString(),
                    'image'      => null,
                ]);
            }

            // إذا كان الدور معلم
            if ($role->name === 'teacher') {
                Teacher::create([
                    'user_id'    => $user->id,
                    'first_name' => 'أحمد',       // غيّرها حسب المطلوب أو استخدم Faker
                    'last_name'  => 'القرآن',
                    'phone'      => '0111111111',
                    'address'    => 'عنوان المعلم',
                    'birthdate'  => now()->subYears(25)->toDateString(),
                    'image'      => null,
                ]);
            }

            $this->command->info("تم إنشاء مستخدم {$role->name} – ID: {$user->id}");
        }
    }
}
