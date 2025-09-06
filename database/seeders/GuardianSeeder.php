<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Guardian;

class GuardianSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstWhere('name', 'guardian');
        if (! $role) {
            $this->command->error("دور guardian غير موجود!");
            return;
        }

        $guardians = [
            [
                'firstname' => 'نوار',
                'lastname'  => 'طلي',
                'phone'     => '0550000001',
                'address'   => 'الرياض',
                'email'     => 'guardian1@gmail.com',
                'password'  => '123123123',
            ],
            [
                'firstname' => 'بشار',
                'lastname'  => 'طلي',
                'phone'     => '0550000002',
                'address'   => 'جدة',
                'email'     => 'guardian2@gmail.com',
                'password'  => '123123123',
            ],
        ];

        foreach ($guardians as $g) {
            $user = User::updateOrCreate(
                ['email' => $g['email']],
                [
                    'password' => Hash::make($g['password']),
                    'role_id'  => $role->id,
                ]
            );

            Guardian::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'firstname' => $g['firstname'],
                    'lastname'  => $g['lastname'],
                    'phone'     => $g['phone'],
                    'address'   => $g['address'],
                ]
            );

            $this->command->info("تم إنشاء/تحديث Guardian: {$g['email']}");
        }
    }
}
