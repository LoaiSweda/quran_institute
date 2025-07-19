<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\UserAd;
use App\Models\EducationClass;

class StudentController extends Controller
{
    public function announcements(Request $request)
    {
        // 1. نأخذ الطالب الحالي
        $student = $request->user();
        $studentId = $student->id;

        // 2. نحصّل معرِّفات الصفوف التي يشارك فيها الطالب
        $classIds = $student
            ->classes()               // علاقة belongsToMany عبر users_classes
            ->pluck('classes.id')     // نأخذ عمود id من جدول classes
            ->toArray();

        // 3. نحصّل معرِّفات المعلمين المالكين لهذه الصفوف
        $teacherIds = EducationClass::query()
            ->whereIn('id', $classIds)   // الصفوف التي في $classIds
            ->pluck('user_id')            // عمود user_id في جدول classes هو صاحب الصفّ (المعلم)
            ->unique()
            ->toArray();

        // 4. نبني الاستعلام لجلب الإعلانات
        $ads = Ad::query()
            // أ) مخصصة للطالب
            ->whereHas('userAds', function($q) {
                $q->where('watches_role', 'student');
            })
            // ب) من معلمين دورهُم = 4
            ->whereIn('user_id', $teacherIds)   // ads.user_id هو معرّف الناشر الحقيقي
            // (اختياري) يمكنك التأكد من role_id أيضاً لو أحببت:
            ->whereHas('publisher', function($q) {
                $q->where('role_id', 4);
            })
            // ج) تحميل العلاقات الضرورية
            ->with([
                'type:id,name',
                'userAds' => function($q) {
                    $q->where('watches_role', 'student');
                },
                'publisher:id,email,role_id',
            ])
            ->orderByDesc('created_at')
            ->get([
                'id','title','description','link','image',
                'end_date','status','type_id','user_id'
            ]);

        return response()->json([
            'data' => $ads
        ], 200);
    }


    public function announcementDetail(Request $request, Ad $ad)
    {
        $user = $request->user();

        // نتأكد أن لهذا الإعلان سجل user_ads بدور 'student'
        $allowed = $ad->userAds()
                    ->where('watches_role', 'student')
                    ->exists();

        if (! $allowed) {
            return response()->json([
                'message' => 'غير مصرح لك بمشاهدة هذا الإعلان.'
            ], 403);
        }

        // نحمّل النوع والناشر
        $ad->load([
            'type:id,name',
            'publisher:id,email,role_id',
        ]);

        return response()->json([
            'data' => [
                'id'              => $ad->id,
                'title'           => $ad->title,
                'description'     => $ad->description,
                'link'            => $ad->link,
                'image_url'       => $ad->image ? asset('storage/'.$ad->image) : null,
                'end_date'        => $ad->end_date,
                'computed_status' => $ad->computed_status,
                'type' => [
                    'id'   => $ad->type->id,
                    'name' => $ad->type->name,
                ],
                'publisher' => [
                    'id'    => optional($ad->publisher)->id,
                    'email' => optional($ad->publisher)->email,
                ],
            ]
        ], 200);
    }

}
