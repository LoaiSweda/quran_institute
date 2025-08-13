<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import the DB facade

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert sample data into the 'categories' table
        DB::table('categories')->insert([
            [
                'name' => 'تجويد', // Tajweed (Recitation Rules)
                'image' => 'images/categories/tajweed.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'تلاوة', // Tilawa (Recitation)
                'image' => 'images/categories/tilawa.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'حفظ', // Hifz (Memorization)
                'image' => 'images/categories/hifz.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'أحاديث', // Hadith (Prophetic Traditions)
                'image' => 'images/categories/hadith.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'فقه', // Fiqh (Islamic Jurisprudence)
                'image' => 'images/categories/fiqh.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

