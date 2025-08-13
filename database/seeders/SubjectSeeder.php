<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Institute;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // أسماء المواد النموذجية
        $defaultSubjects = [
            'القرآن الكريم',
            'التفسير',
            'الحديث الشريف',
            'الفقه',
            'اللغة العربية',
            'القراءة والتجويد',
        ];

        // لكل معهد موجود في قاعدة البيانات
        Institute::all()->each(function(Institute $institute) use ($defaultSubjects) {
            // ننشئ لكل معهد ثلاث مواد عشوائية من القائمة
            shuffle($defaultSubjects);
            $subjectsToCreate = array_slice($defaultSubjects, 0, 3);

            foreach ($subjectsToCreate as $name) {
                Subject::create([
                    'name'         => $name,
                    'institute_id' => $institute->id,
                ]);
            }
        });
    }
}
