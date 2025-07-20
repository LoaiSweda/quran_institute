<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            TeacherSeeder::class,  
            GuardianSeeder::class,
            AdsTypeSeeder::class, 
            AdSeeder::class,
            UserAdSeeder::class, 
            InstituteSeeder::class,
            SubjectSeeder::class,
            EducationClassSeeder::class,
            StudentSeeder::class,
            SessionScheduleSeeder::class,
            UserClassSeeder::class,
            InstituteUserSeeder::class,
        ]);
    }
}
