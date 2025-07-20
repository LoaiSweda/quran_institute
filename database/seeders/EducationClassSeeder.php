<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EducationClass;

class EducationClassSeeder extends Seeder
{
    public function run(): void
    {
        $subjectId = 1; // عدّل هذا حسب الـ subject الفعلي الذي تريد ربط الصف به

        EducationClass::create([
            'name'               => 'الصف الأول',
            'subject_id'         => $subjectId,
            'students_count'     => 25,
            'user_id'            => 4,
            'session_count'      => 16,
            'qr'                 => 'QR101',
            'present_percentage' => 80,
        ]);

        // وهكذا لبقية الصفوف:
        EducationClass::create([
            'name'               => 'الصف الثاني',
            'subject_id'         => $subjectId,
            'students_count'     => 22,
            'user_id'            => 4,
            'session_count'      => 18,
            'qr'                 => 'QR102',
            'present_percentage' => 75,
        ]);

        // ... أكمل لباقي الصفوف بنفس الطريقة
    }
}
