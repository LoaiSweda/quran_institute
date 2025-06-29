<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EducationClass;

class EducationClassSeeder extends Seeder
{
    public function run(): void
    {
        // ننشئ خمسة صفوف افتراضية
        EducationClass::create([
            'name'               => 'الصف الأول',
            'students_count'     => 25,
            'user_id'            => 4,    // رقم أستاذ مفترض
            'session_count'      => 16,
            'qr'                 => 'QR101',
            'present_percentage' => 80,
        ]);

        EducationClass::create([
            'name'               => 'الصف الثاني',
            'students_count'     => 22,
            'user_id'            => 4,
            'session_count'      => 18,
            'qr'                 => 'QR102',
            'present_percentage' => 75,
        ]);

        EducationClass::create([
            'name'               => 'الصف الثالث',
            'students_count'     => 20,
            'user_id'            => 4,
            'session_count'      => 20,
            'qr'                 => 'QR103',
            'present_percentage' => 85,
        ]);

        EducationClass::create([
            'name'               => 'الصف الرابع',
            'students_count'     => 18,
            'user_id'            => 4,
            'session_count'      => 16,
            'qr'                 => 'QR104',
            'present_percentage' => 70,
        ]);

        EducationClass::create([
            'name'               => 'الصف الخامس',
            'students_count'     => 30,
            'user_id'            => 4,
            'session_count'      => 22,
            'qr'                 => 'QR105',
            'present_percentage' => 90,
        ]);
    }
}
