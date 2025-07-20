<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CertificateRequest;
use App\Models\Student;
use App\Models\Subject;
use Carbon\Carbon;

class CertificateRequestSeeder extends Seeder
{
    public function run(): void
    {
        // لكل طالب، لكل مادة يدرسها، أنشئ طلب شهادة عشوائي
        Student::all()->each(function (Student $student) {
            $student->classes()->each(function ($class) use ($student) {
                CertificateRequest::create([
                    'student_id'    => $student->id,
                    'subject_id'    => $class->subject_id,
                    'request_at'    => Carbon::now()->subDays(rand(1, 10)),
                    'status'        => collect(['pending','approved','rejected'])->random(),
                    'user_id'       => $student->user_id,
                    'revieweded_at' => rand(0,1)
                        ? Carbon::now()->subDays(rand(0, 5))
                        : null,
                    'file_id'       => null, // أو ضع ID موجود من جدول files إذا لديك ملفات
                ]);
            });
        });
    }
}
