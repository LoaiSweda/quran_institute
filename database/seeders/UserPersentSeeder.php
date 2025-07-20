<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserPersent;
use App\Models\Student;
use App\Models\EducationClass;
use App\Models\Persent;

class UserPersentSeeder extends Seeder
{
    public function run(): void
    {
        $persents = Persent::all();

        // لكل طالب، ولكل حلقة، ولكل persent، سجل حضور/غياب عشوائي
        Student::all()->each(function (Student $student) use ($persents) {
            $student->classes()->each(function (EducationClass $class) use ($student, $persents) {
                $persents->each(function ($p) use ($student, $class) {
                    UserPersent::create([
                        'user_id'    => $student->user_id,
                        'class_id'   => $class->id,
                        'persent_id' => $p->id,
                        'status'     => (bool) rand(0, 1) ? 'present' : 'absent',
                    ]);
                });
            });
        });
    }
}
