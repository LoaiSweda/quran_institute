<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Ad;
use App\Models\User;
use App\Models\AdsType;

class AdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // نبحث عن أول مستخدم له دور teacher
        $teacher = User::whereHas('role', fn($q) => $q->where('name', 'teacher'))->first();
        if (! $teacher) {
            $this->command->error("لم يُعثر على مستخدم teacher لإنشاء الإعلانات.");
            return;
        }

        // نجلب قائمة الأنواع مع الـ id
        $types = AdsType::pluck('id', 'name')->toArray();

        // بيانات إعلانات تجريبية
        $adsData = [
            [
                'title'       => 'إعلان بانر رمضان',
                'description' => 'سجل الآن في دورة التحفيظ الرمضانية.',
                'link'        => 'https://example.com/ramadan-course',
                'end_date'    => Carbon::now()->addDays(10),
                'type_id'     => $types['banner'] ?? null,
                'status'      => 'active',
            ],
            [
                'title'       => 'نافذة منبثقة: مسابقة حفظ',
                'description' => 'شارك في مسابقة الحفظ الأسبوعية واربح جوائز.',
                'link'        => 'https://example.com/contest',
                'end_date'    => Carbon::now()->addDays(5),
                'type_id'     => $types['popup'] ?? null,
                'status'      => 'active',
            ],
            [
                'title'       => 'فيديو تعريفي بالمعهد',
                'description' => 'شاهد الفيديو للتعرف على أنشطتنا.',
                'link'        => 'https://example.com/intro-video',
                'end_date'    => Carbon::now()->addDays(30),
                'type_id'     => $types['video'] ?? null,
                'status'      => 'active',
            ],
        ];

        foreach ($adsData as $data) {
            Ad::updateOrCreate(
                [
                    'user_id'    => $teacher->id,
                    'title'      => $data['title'],
                ],
                array_merge($data, ['user_id' => $teacher->id])
            );
        }

        $this->command->info('تمّت تعبئة الإعلانات التجريبية للمعلم.');
    }
}
