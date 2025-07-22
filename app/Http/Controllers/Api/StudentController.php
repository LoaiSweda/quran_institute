<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// موديلاتك
use App\Models\Ad;
use App\Models\User;
use App\Models\EducationClass;
use App\Models\StudentProgress;
use App\Models\Exam;
use App\Models\UserPersent;
use App\Models\CertificateRequest;
use App\Models\File;
use App\Models\Teacher;

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
        $instIds = EducationClass::query()
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

        // 5) دمج الثلاث مجموعات
        $publisherIds = array_unique(array_merge($instUserIds, $superIds));

        // 6) استعلام الإعلانات
        $ads = Ad::query()
            ->whereIn('user_id', $publisherIds)
            ->whereHas('userAds', fn($q) => $q->where('watches_role', 'student'))
            ->with(['type:id,name', 'publisher:id,name,email,role_id'])
            ->orderByDesc('created_at')
            ->get([
                'id', 'title', 'description', 'link',
                'image', 'end_date', 'status', 'type_id', 'user_id'
            ]);

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

        $teacherId = $class->user_id;

        $ads = Ad::query()
            ->where('user_id', $teacherId)
            ->whereHas('userAds', fn($q) => $q->where('watches_role', 'student'))
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

        // تأكد أن الإعلان موجه للطالب
        if (! $ad->userAds()->where('watches_role', 'student')->exists()) {
            return response()->json([
                'message' => 'غير مصرح لك بمشاهدة هذا الإعلان.'
            ], 403);
        }

        // تحميل العلاقات الضرورية
        $ad->load([
            'type:id,name',
            'publisher:id,email,role_id',
            'userAds'
        ]);

        $data = [
            'id'              => $ad->id,
            'title'           => $ad->title,
            'description'     => $ad->description,
            'link'            => $ad->link,
            'image_url'       => $ad->image ? asset('storage/'.$ad->image) : null,
            'end_date'        => $ad->end_date,
            'computed_status' => $ad->computed_status,
            'type'            => [
                'id'   => $ad->type->id,
                'name' => $ad->type->name,
            ],
            'publisher' => [
                'id'      => $ad->publisher->id,
                'email'   => $ad->publisher->email,
                'role_id' => $ad->publisher->role_id,
            ],
            'watches_role' => $ad->userAds
                                  ->firstWhere('watches_role', 'student')
                                  ->watches_role ?? null,
        ];

        return response()->json(['data' => $data], 200);
    }

    /**
     * قائمة الصفوف للمستخدم (طالب)
     */
    public function classes(Request $request)
    {
        $user = $request->user();

        $classes = $user
            ->classes()
            ->with(['subject', 'teacher.user'])
            ->get();

        $result = $classes->map(fn($class) => [
            'id'                 => $class->id,
            'name'               => $class->name,
            'subject'            => [
                'id'          => $class->subject->id,
                'name'        => $class->subject->name,
                'image'       => $class->subject->image,
                'description' => $class->subject->description,
                'start_date'  => $class->subject->start_date
                                    ? $class->subject->start_date->toDateString()
                                    : null,
                'end_date'    => $class->subject->end_date
                                    ? $class->subject->end_date->toDateString()
                                    : null,
                'level'       => $class->subject->level,
                'degree'      => $class->subject->degree,
                'is_active'   => (bool) $class->subject->is_active,
            ],
            'teacher'            => [
                'id'         => $class->teacher->id,
                'first_name' => $class->teacher->user->first_name,
                'last_name'  => $class->teacher->user->last_name,
                'email'      => $class->teacher->user->email,
            ],
            'students_count'     => $class->students_count,
            'session_count'      => $class->session_count,
            'qr'                 => $class->qr,
            'present_percentage' => $class->present_percentage,
        ]);

        return response()->json(['classes' => $result], 200);
    }

    /**
     * تفاصيل الصف للمستخدم (طالب)
     */
    public function classDetail(Request $request, EducationClass $class)
    {
        $studentUser = $request->user();

        // تأكد أن الطالب مسجّل في هذه الحلقة
        if (! $studentUser->classes()->where('classes.id', $class->id)->exists()) {
            return response()->json([
                'message' => 'غير مصرح لك بمشاهدة تفاصيل هذه الحلقة.'
            ], 403);
        }

        // تحميل العلاقات الأساسية
        $class->load(['subject', 'teacher', 'sessionSchedules.persents.userPersents.user.studentProfile']);

        $stud = $studentUser->studentProfile;

        // تقدّم الطالب
        $prog = $stud->progress()
                     ->where('class_id', $class->id)
                     ->first();

        // امتحانات الطالب
        $exams = $stud->exams()
                      ->where('class_id', $class->id)
                      ->get();

        // نسب الحضور الكاملة
        $persents = UserPersent::where([
                        ['user_id',  $studentUser->id],
                        ['class_id', $class->id],
                    ])
                    ->get()
                    ->map(fn($p) => [
                        'date'       => $p->persent->date->toDateString(),
                        'time'       => $p->persent->time->format('H:i'),
                        'status'     => $p->status,
                    ]);

        // طلبات الشهادة
        $certs = CertificateRequest::with('file')
                    ->where([
                        ['student_id', $stud->id],
                        ['subject_id', $class->subject_id],
                    ])->get();

        // بيانات الطالب
        $studentDetail = [
            'user' => [
                'id'         => $studentUser->id,
                'first_name' => $stud->first_name,
                'last_name'  => $stud->last_name,
                'phone'      => $stud->phone,
                'address'    => $stud->address,
                'birthdate'  => $stud->birthdate?->toDateString(),
            ],
            'progress' => $prog ? [
                'observation_rate'         => $prog->eohservation_rate,
                'degree_avg'               => $prog->degree_avg,
                'number_sessions_attended' => $prog->number_sessions_attended,
                'total_points_subject'     => $prog->total_points_subject,
            ] : null,
            'exams'        => $exams->map(fn($e) => [
                                 'id'    => $e->id,
                                 'name'  => $e->name,
                                 'notes' => $e->notes,
                                 'points'=> $e->points,
                                 'degree'=> $e->degree,
                             ]),
            'persents'     => $persents,
            'certificates' => $certs->map(fn($c) => [
                                 'id'          => $c->id,
                                 'request_at'  => $c->request_at,
                                 'status'      => $c->status,
                                 'reviewed_at' => $c->revieweded_at,
                                 'file'        => $c->file ? [
                                     'id'   => $c->file->id,
                                     'name' => $c->file->name,
                                     'path' => asset('storage/' . $c->file->path),
                                     'size' => $c->file->size,
                                     'mime' => $c->file->mime,
                                 ] : null,
                             ]),
        ];

        // بناء الرد النهائي
        $data = [
            'id'                 => $class->id,
            'name'               => $class->name,
            'qr'                 => $class->qr,
            'students_count'     => $class->students_count,
            'session_count'      => $class->session_count,
            'present_percentage' => $class->present_percentage,
            'subject'            => [
                'id'             => $class->subject->id,
                'name'           => $class->subject->name,
                'description'    => $class->subject->description,
                'start_date'     => $class->subject->start_date?->toDateString(),
                'end_date'       => $class->subject->end_date?->toDateString(),
                'level'          => $class->subject->level,
                'degree'         => $class->subject->degree,
                'total_sessions' => $class->subject->total_sessions,
                'is_active'      => (bool) $class->subject->is_active,
                'image_url'      => $class->subject->image_url ?? null,
            ],
            'teacher' => [
                'id'         => $class->teacher->user_id,
                'first_name' => $class->teacher->first_name,
                'last_name'  => $class->teacher->last_name,
                'email'      => $class->teacher->user->email,
            ],
            'session_schedules' => $class->sessionSchedules->map(fn($s) => [
                'id'          => $s->id,
                'day_of_week' => $s->day_of_week,
                'start_time'  => $s->start_time->format('H:i'),
                'end_time'    => $s->end_time->format('H:i'),
                'attendees'   => $s->persents
                                    ->where('date', Carbon::now()->toDateString())
                                    ->flatMap(fn($p) => $p->userPersents
                                        ->where('status', 'present')
                                        ->map(fn($up) => [
                                            'id'         => $up->user_id,
                                            'first_name' => $up->user->studentProfile->first_name ?? $up->user->first_name,
                                            'last_name'  => $up->user->studentProfile->last_name  ?? $up->user->last_name,
                                        ])
                                    ),
            ]),
            'student' => $studentDetail,
        ];

        return response()->json(['data' => $data], 200);
    }

    /**
     * بروفايل الطالب
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $student = $user->studentProfile;

        if (! $student) {
            return response()->json([
                'message' => 'لا يوجد ملف للطالب'
            ], 404);
        }

        return response()->json([
            'data' => [
                'id'                => $student->id,
                'first_name'        => $student->first_name,
                'last_name'         => $student->last_name,
                'phone'             => $student->phone,
                'address'           => $student->address,
                'birthdate'         => $student->birthdate?->toDateString(),
                'guardian_id'       => $student->guardian_id,
                'present_percentage'=> $student->present_percentage,
                'qr'                => $student->qr,
            ]
        ], 200);
    }
}
