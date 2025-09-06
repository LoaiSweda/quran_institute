<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Institute;
use App\Models\User;

class InstituteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $managers = User::where('role_id', 2)->take(3)->get();

        foreach ($managers as $index => $manager) {
            Institute::create([
                'name'    => "المعهد التجريبي " . ($index + 1),
                'address' => "عنوان المعهد رقم " . ($index + 1),
                'image'   => null,              
                'user_id' => $manager->id,      
            ]);
        }

        $superAdmin = User::where('role_id', 1)->first();
        if ($superAdmin) {
            Institute::create([
                'name'    => "المعهد المركزي",
                'address' => "المقر الرئيسي",
                'image'   => null,
                'user_id' => $superAdmin->id,
            ]);
        }
    }
}
