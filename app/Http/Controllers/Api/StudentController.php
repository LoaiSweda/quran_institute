<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\UserAd;

class StudentController extends Controller
{
    /**
     * إرجاع قائمة الإعلانات الموجّهة للطالب الحالي
     */
    public function announcements(Request $request)
    {
        $studentId = $request->user()->id;

        // نختار كل إعلان له سجل user_ads بدور 'student'
        $ads = Ad::whereHas('userAds', function($q) {
                $q->where('watches_role', 'student');
            })
            ->with([
                'type:id,name',
                // نضمّ أيضًا بيانات الناشر (المعلم)
                'userAds' => function($q) {
                    $q->where('watches_role', 'student');
                },
                // لتحميل بيانات المعلم (الناشر) إذا أردنا اسمه أو بريده
                'publisher:id,email'
            ])
            ->orderByDesc('created_at')
            ->get([
                'id','title','description','link','image','end_date','status','type_id'
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
                      ->where('publish_id', '!=', null) // أو أي شرط إضافي
                      ->exists();

        if (! $allowed) {
            return response()->json([
                'message' => 'غير مصرح لك بمشاهدة هذا الإعلان.'
            ], 403);
        }

        // نحمّل العلاقات الضرورية
        $ad->load([
            'type:id,name',
            'publisher:id,email',
            // أو عبر العلاقة belongsToMany
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
                'publisher' => $ad->publisher->map(fn($u)=>[
                    'id'    => $u->id,
                    'email' => $u->email,
                ]),
            ]
        ], 200);
    }
}
