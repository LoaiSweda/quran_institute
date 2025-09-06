<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Admin;
use App\Models\Teacher;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $roles = Role::whereIn('name', [
            'super admin',
            'admin',
            'institute manager',
            'teacher',
        ])->get();

        foreach ($roles as $role) {
            $emailSlug = Str::slug($role->name, '_'); 
            $user = User::create([
                'email'    => "{$emailSlug}@gmail.com",
                'password' => Hash::make('123123123'),
                'role_id'  => $role->id,
            ]);

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

            if ($role->name === 'teacher') {
                Teacher::create([
                    'user_id'    => $user->id,
                    'first_name' => 'أحمد',      
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
