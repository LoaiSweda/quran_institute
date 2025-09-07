<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Ad;
use App\Models\User;
use App\Models\EducationClass;
use App\Models\StudentProgress;
use App\Models\Exam;
use App\Models\UserPersent;
use App\Models\CertificateRequest;
use App\Models\File;
use App\Models\Teacher;
use App\Models\SessionSchedule;

class StudentController extends Controller
{
    public function instituteAnnouncements(Request $request)
    {
        $auth = $request->user();

        // 1) حصص المستخدم نفسه (إن كان طالبًا)
        $selfClassIds = $auth->classes()->pluck('classes.id')->toArray();

        // 2) حصص أبناء الولي عبر guardians → students (بدون توابع)
        $guardianId = DB::table('guardians')->where('user_id', $auth->id)->value('id'); // قد تكون null
        $childUserIds = $guardianId
            ? DB::table('students')->where('guardian_id', $guardianId)->pluck('user_id')->toArray()
            : [];

        $childClassIds = !empty($childUserIds)
            ? DB::table('users_classes')->whereIn('user_id', $childUserIds)->pluck('class_id')->toArray()
            : [];

        // 3) دمج كل الحصص (الولي + الأبناء)
        $classIds = array_values(array_unique(array_merge($selfClassIds, $childClassIds)));

        // 4) مؤسسات هذه الحصص
        $instIds = !empty($classIds)
            ? EducationClass::query()
                ->whereIn('classes.id', $classIds)
                ->join('subjects', 'classes.subject_id', '=', 'subjects.id')
                ->pluck('subjects.institute_id')->unique()->toArray()
            : [];

        // 5) ناشرو المؤسسة + السوبر أدمن
        $instUserIds = !empty($instIds)
            ? DB::table('institute_user')->whereIn('institute_id', $instIds)->pluck('user_id')->unique()->toArray()
            : [];
        $superIds     = User::where('role_id', 1)->pluck('id')->toArray();
        $publisherIds = array_values(array_unique(array_merge($instUserIds, $superIds)));

        // 6) الإعلانات الموجّهة للطالب/الولي
        $ads = Ad::query()
            ->when(!empty($publisherIds) && !empty($instIds), function ($q) use ($publisherIds, $instIds) {
                $q->whereHas('userAds', function ($qq) use ($publisherIds) {
                    $qq->whereIn('publish_id', $publisherIds)
                       ->whereIn(DB::raw('LOWER(watches_role)'), ['student','guardian']);
                })->orWhereIn('institute_id', $instIds);
            })
            ->when(!empty($publisherIds) && empty($instIds), function ($q) use ($publisherIds) {
                $q->whereHas('userAds', function ($qq) use ($publisherIds) {
                    $qq->whereIn('publish_id', $publisherIds)
                       ->whereIn(DB::raw('LOWER(watches_role)'), ['student','guardian']);
                });
            })
            ->when(empty($publisherIds) && !empty($instIds), function ($q) use ($instIds) {
                $q->whereIn('institute_id', $instIds);
            })
            ->with(['type:id,name', 'publisher:id,email,role_id'])
            ->orderByDesc('created_at')
            ->get(['id','title','description','link','image','end_date','status','type_id','user_id','institute_id'])
            ->map(function ($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'description' => $ad->description,
                    'link' => $ad->link,
                    'image_url' => $ad->image ? asset('storage/app/public/'.$ad->image) : null,
                    'end_date' => $ad->end_date,
                    'status' => $ad->status,
                    'computed_status' => $ad->computed_status,
                    'type' => ['id' => optional($ad->type)->id, 'name' => optional($ad->type)->name],
                    'publisher' => [
                        'id' => optional($ad->publisher)->id,
                        'email' => optional($ad->publisher)->email,
                        'role_id' => optional($ad->publisher)->role_id,
                    ],
                ];
            });

        return response()->json(['data' => $ads], 200);
    }

    public function classAnnouncements(Request $request, EducationClass $class)
    {
        $student = $request->user();

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

        $ads->transform(function ($ad) {
            if ($ad->image) {
                $ad->image = asset('storage/app/public/' . $ad->image);
            }
            return $ad;
        });

        return response()->json(['data' => $ads], 200);
    }

    public function announcementDetail(Request $request, Ad $ad)
    {
        $student = $request->user();

        if (! $ad->userAds()->where('watches_role', 'student')->exists()) {
            return response()->json([
                'message' => 'غير مصرح لك بمشاهدة هذا الإعلان.'
            ], 403);
        }

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
            'image_url'       => $ad->image ? asset('storage/app/public/'.$ad->image) : null,
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

    public function classes(Request $request)
    {
        $user = $request->user();

        $classes = $user
            ->classes()
            ->with(['subject', 'teacher.user'])
            ->get();

        $result = $classes->map(function ($class) {
            $subjectImage = $class->subject->image ? asset('storage/app/public/' . $class->subject->image) : null;

            return [
                'id'                 => $class->id,
                'name'               => $class->name,
                'subject'            => [
                    'id'          => $class->subject->id,
                    'name'        => $class->subject->name,
                    'image'       => $subjectImage,
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
            ];
        });

        return response()->json(['classes' => $result], 200);
    }

    public function classDetail(Request $request, EducationClass $class)
    {
        $studentUser = $request->user();

        if (! $studentUser->classes()->where('classes.id', $class->id)->exists()) {
            return response()->json([
                'message' => 'غير مصرح لك بمشاهدة تفاصيل هذه الحلقة.'
            ], 403);
        }

        $class->load(['subject', 'teacher', 'sessionSchedules.persents.userPersents.user.studentProfile']);

        $stud = $studentUser->studentProfile;

        $prog = $stud->progress()
                     ->where('class_id', $class->id)
                     ->first();

        $exams = $stud->exams()
                      ->where('class_id', $class->id)
                      ->get();

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

        $certs = CertificateRequest::with('file')
                    ->where([
                        ['student_id', $stud->id],
                        ['subject_id', $class->subject_id],
                    ])->get();

        $subjectImage = $class->subject->image ? asset('storage/app/public/' . $class->subject->image) : null;

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
                                     'path' => asset('storage/app/public/' . $c->file->path),
                                     'size' => $c->file->size,
                                     'mime' => $c->file->mime,
                                 ] : null,
                             ]),
        ];

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
                'image_url'      => $subjectImage,
            ],
            'teacher' => [
                'id'         => $class->teacher->user_id,
                'first_name' => $class->teacher->first_name,
                'last_name'  => $class->teacher->last_name,
                'email'      => $class->teacher->user->email,
            ],
            // لاحظ: نُرجع اليوم بالعربية + رقم اليوم لضمان التوافق مع الواجهات
            'session_schedules' => $class->sessionSchedules->map(fn($s) => [
                'id'           => $s->id,
                'day_index'    => $s->day_of_week_index,      // 0..6
                'day_of_week'  => $s->day_of_week_name_ar,    // بالعربية
                'start_time'   => $s->start_time->format('H:i'),
                'end_time'     => $s->end_time->format('H:i'),
                'attendees'    => $s->persents
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

    public function weeklySchedule(Request $request)
    {
        $user = $request->user();

        // 1) الحلقات الخاصة بالمستخدم
        $classIds = $user->classes()->pluck('classes.id')->toArray();

        // 2) نبني الاستعلام مع خيار فلترة اليوم (?day=0 أو sunday أو الأحد)
        $q = SessionSchedule::with(['educationClass.subject'])
            ->whereIn('class_id', $classIds)
            ->whereHas('educationClass')
            ->whereHas('educationClass.subject');

        if ($request->filled('day')) {
            $q->whereDow($request->query('day'));
        }

        // الترتيب الصحيح للأسبوع حتى لو القيم نصية
        $schedules = $q->orderByDow()
                       ->orderBy('start_time')
                       ->get(['id','class_id','day_of_week','start_time','end_time']);

        // 3) مصفوفة الأيام بالعربية
        $days = SessionSchedule::DAYS_AR;

        // 4) بناء الناتج: مفاتيح عربية والقيم حصص اليوم
        $weekly = [];
        foreach ($days as $label) {
            $weekly[$label] = [];
        }

        foreach ($schedules as $sch) {
            $label = $sch->day_of_week_name_ar ?? (string) $sch->day_of_week; // fallback لو غير معروف
            if (! array_key_exists($label, $weekly)) {
                $weekly[$label] = [];
            }

            $weekly[$label][] = [
                'schedule_id' => $sch->id,
                'day_index'   => $sch->day_of_week_index,
                'class'       => [
                    'id'   => $sch->educationClass->id,
                    'name' => $sch->educationClass->name,
                ],
                'subject'     => [
                    'id'   => $sch->educationClass->subject->id,
                    'name' => $sch->educationClass->subject->name,
                ],
                'start_time'  => $sch->start_time->format('H:i'),
                'end_time'    => $sch->end_time->format('H:i'),
            ];
        }

        return response()->json([
            'data' => $weekly,
        ], 200);
    }
}
