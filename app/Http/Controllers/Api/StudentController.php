<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ad;
use App\Models\EducationClass;
use App\Models\User;

class StudentController extends Controller
{
    /**
     * 1) إعلانات المعاهد:
     *    - super-admin (1): كل إعلاناته
     *    - مدير المعهد (2) أو مشرف (3) في معهد الطالب
     */
    public function instituteAnnouncements(Request $request)
    {
        $student = $request->user();

        // 1) كل الـ class IDs للطالب
        $classIds = $student->classes()
                            ->pluck('classes.id')
                            ->toArray();

        // 2) عن طريق جدول classes → subjects تحدد institute_id
        $instIds = \App\Models\EducationClass::query()
            ->whereIn('classes.id', $classIds)
            ->join('subjects', 'classes.subject_id', '=', 'subjects.id')
            ->pluck('subjects.institute_id')
            ->unique()
            ->toArray();

        // 3) جلب مدراء ومشرفين هذه المعاهد
        $instUserIds = DB::table('institute_user')
            ->whereIn('institute_id', $instIds)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        // 4) جلب جميع super-admin
        $superIds = User::where('role_id', 1)
                        ->pluck('id')
                        ->toArray();

        // 5) دمج الثلاث مجموعات: super-admins + managers + supervisors
        $publisherIds = array_unique(array_merge($instUserIds, $superIds));

        // 6) استعلام الإعلانات
        $ads = Ad::query()
            ->whereIn('user_id', $publisherIds)                       
            ->whereHas('userAds', fn($q)=> $q->where('watches_role','student'))
            ->with(['type:id,name','publisher:id,email,role_id'])
            ->orderByDesc('created_at')
            ->get(['id','title','description','link',
                'image','end_date','status','type_id','user_id']);

        return response()->json(['data' => $ads], 200);
    }


    /**
     * 2) إعلانات المعلمين لصف معين:
     *    - المعلم صاحب الـ class
     */
    public function classAnnouncements(Request $request, EducationClass $class)
    {
        $student = $request->user();

        // تأكد أن الطالب مسجَّل في الصف
        if (! $student->classes()->where('classes.id', $class->id)->exists()) {
            return response()->json([
                'message' => 'غير مصرح لك بمشاهدة هذه الإعلانات.'
            ], 403);
        }

        // صاحب الإعلان هو المعلم (user_id) للصف
        $teacherId = $class->user_id;

        $ads = Ad::query()
            ->where('user_id', $teacherId)
            ->whereHas('userAds', fn($q) => $q->where('watches_role','student'))
            ->with(['type:id,name','publisher:id,email,role_id'])
            ->orderByDesc('created_at')
            ->get([
                'id','title','description','link',
                'image','end_date','status','type_id','user_id'
            ]);

        return response()->json(['data' => $ads], 200);
    }

      /**
     * عرض تفاصيل إعلان واحد
     */
    public function announcementDetail(Request $request, Ad $ad)
    {
        $student = $request->user();

        // 1) نتأكد أن الإعلان موجه للطالب
        $hasAccess = $ad->userAds()
                        ->where('watches_role', 'student')
                        ->exists();

        if (! $hasAccess) {
            return response()->json([
                'message' => 'غير مصرح لك بمشاهدة هذا الإعلان.'
            ], 403);
        }

        // 2) تحميل العلاقات الضرورية
        $ad->load([
            'type:id,name',
            'publisher:id,email,role_id',
            'userAds'  // لو احتجت معلومات إضافية من pivot
        ]);

        // 3) إعداد الهيكل النهائي للـ JSON
        $data = [
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
                'id'      => $ad->publisher->id,
                'email'   => $ad->publisher->email,
                'role_id' => $ad->publisher->role_id,
            ],
            // لو احتجت بيانات pivot:
            'watches_role' => $ad->userAds
                                  ->firstWhere('watches_role', 'student')
                                  ->watches_role ?? null,
        ];

        return response()->json([
            'data' => $data
        ], 200);
    }


    /**
     * إرجاع حلقات الطالب المسجل دخوله
     */
    public function classes(Request $request)
    {
        // 1) الطالب المُسجّل دخوله
        $user = $request->user();
        
        // 2) جلب الحلقات المرتبطة به مع بعض العلاقات المفيدة
        $classes = $user
            ->classes()                // من علاقة belongsToMany في موديل User
            ->with([
                'subject',             // بيانات المادّة
                'teacher.user'         // بيانات المدرّس (من Teacher → User)
            ])
            ->get();

        // 3) ترجيع JSON مُنسّق
        return response()->json([
            'classes' => $classes->map(function($class) {
                return [
                    'id'                => $class->id,
                    'name'              => $class->name,
                    'subject'           => [
                        'id'    => $class->subject->id,
                        'name'  => $class->subject->name,
                    ],
                    'teacher'           => [
                        'id'         => $class->teacher->id,
                        'first_name' => $class->teacher->user->first_name,
                        'last_name'  => $class->teacher->user->last_name,
                        'email'      => $class->teacher->user->email,
                    ],
                    'students_count'    => $class->students_count,
                    'session_count'     => $class->session_count,
                    'qr'                => $class->qr,
                    'present_percentage'=> $class->present_percentage,
                ];
            }),
        ]);
    }
}
