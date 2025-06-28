<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdsType;

class AdsTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'banner',
            'popup',
            'video',
            'sidebar',
        ];

        foreach ($types as $name) {
            AdsType::firstOrCreate(['name' => $name]);
        }

        $this->command->info('تمّت تعبئة أنواع الإعلانات.');
    }
}
