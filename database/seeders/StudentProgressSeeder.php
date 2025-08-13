<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\StudentProgress;

class StudentProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // لكل طالب، لكل حلقة مسجل فيها
        Student::all()->each(function ($student) {
            $student->classes()->each(function ($class) use ($student) {
                StudentProgress::create([
                    'student_id'               => $student->id,
                    'class_id'                 => $class->id,
                    'eohservation_rate'        => rand(50, 100),    // نسبة المراقبة
                    'degree_avg'               => rand(60, 100),    // متوسط الدرجات
                    'number_sessions_attended' => rand(0, $class->session_count),
                    'total_points_subject'     => rand(100, 300),   // مجموع النقاط
                ]);
            });
        });
    }
}
