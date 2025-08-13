<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Student;
use App\Models\EducationClass;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        // لكل طالب، ولكل حلقة يشارك فيها، أنشئ 2 امتحان افتراضي
        Student::all()->each(function (Student $student) {
            $student->classes()->each(function (EducationClass $class) use ($student) {
                Exam::create([
                    'class_id'   => $class->id,
                    'student_id' => $student->id,
                    'name'       => 'اختبار منتصف الفصل',
                    'notes'      => 'نموذج امتحان منتصف الفصل الدراسي',
                    'points'     => rand(10, 30),
                    'degree'     => rand(30, 50),
                ]);

                Exam::create([
                    'class_id'   => $class->id,
                    'student_id' => $student->id,
                    'name'       => 'اختبار نهاية الفصل',
                    'notes'      => 'نموذج امتحان نهاية الفصل الدراسي',
                    'points'     => rand(20, 40),
                    'degree'     => rand(40, 60),
                ]);
            });
        });
    }
}
