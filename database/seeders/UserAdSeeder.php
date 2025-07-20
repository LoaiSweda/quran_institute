<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ad;
use App\Models\UserAd;

class UserAdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الأدوار التي نريد أن نربطها بكل إعلان
        $roles = ['student', 'guardian'];

        // نجلب كل الإعلانات
        Ad::all()->each(function (Ad $ad) use ($roles) {
            foreach ($roles as $role) {
                UserAd::create([
                    'publish_id'   => $ad->user_id,  // صاحب الإعلان من جدول users
                    'ads_id'       => $ad->id,       // الإعلان نفسه
                    'watches_role' => $role,         // دور المشاهد
                ]);
            }
        });
    }
}
