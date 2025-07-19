<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\Institute;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Storage;
//use BaconQrCode\Renderer\ImageRenderer;
//use BaconQrCode\Renderer\RendererStyle\RendererStyle;
//use BaconQrCode\Renderer\Image\SvgImageBackEnd;
//use BaconQrCode\Writer;

class ClassesController extends Controller
{
    /**
     * احصل على معهد المدير الحالي أو ارمي 403
     *
     * @return Institute
     */
    protected function currentInstitute(): Institute
    {
        $inst = Institute::where('user_id', auth()->id())->first();
        abort_if(!$inst, 403, 'لا تملك صلاحية على أي معهد.');
        return $inst;
    }

    /**
     * عرض قائمة الحلقات الخاصة بمعهد المدير
     */
    public function index()
    {
        $inst = $this->currentInstitute();

        $classes = EducationClass::whereHas('subject', fn($q)=>
        $q->where('institute_id',$inst->id))
            ->with(['subject','teacher'])
            ->paginate(20);

        return view('manager.classes.index', compact('classes'));
    }


    /**
     * عرض نموذج إنشاء حلقة جديدة
     */

    /**
     * نموذج إنشاء حلقة + عرض الحلقات المنشأة
     */
    public function create()
    {
        $inst     = $this->currentInstitute();
        $subjects = Subject::where('institute_id', $inst->id)->get();

        // جلب المدرسين (first_name & last_name) عبر pivot institute_user + جدول teachers
        $teachers = DB::table('institute_user')
            ->join('teachers', 'institute_user.user_id', '=', 'teachers.user_id')
            ->where('institute_user.institute_id', $inst->id)
            ->where('institute_user.role_institute', 'teacher')
            ->select([
                'teachers.user_id as id',
                'teachers.first_name',
                'teachers.last_name',
            ])
            ->get();

        // أيضاً جلب الحلقات المنشأة سابقاً لعرضها تحت الفورم
        $classes = EducationClass::whereHas('subject', function($q) use($inst) {
            $q->where('institute_id', $inst->id);
        })
            ->with(['subject','teacher'])
            ->paginate(10);

        return view('manager.classes.create', compact('subjects','teachers','classes'));
    }



    /**
     * حفظ حلقة جديدة وتوليد QR بصيغة SVG
     */

    public function store(Request $request)
    {
        $inst = $this->currentInstitute();

        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'subject_id'     => 'required|exists:subjects,id',
            'user_id'        => 'required|exists:users,id',   // هذا هو الـ teacher
            'students_count' => 'required|integer|min:1',
            // إضافة التحقق للجداول
            'schedules'      => 'nullable|array',
            'schedules.*.day_of_week' => 'required_with:schedules|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'schedules.*.start_time'  => 'required_with:schedules|date_format:H:i',
            'schedules.*.end_time'    => 'required_with:schedules|date_format:H:i|after:schedules.*.start_time',

        ]);
        DB::transaction(function() use ($data, $inst, $request, &$cls) {

            // 1) إنشاء الحلقة
            $cls = EducationClass::create($data);

            // 2) تأكد أولًا أنّ المدرّس موجود كعضو في المعهد
            DB::table('institute_user')->updateOrInsert(
                [
                    'institute_id' => $inst->id,
                    'user_id' => $data['user_id'],
                ],
                [
                    'role_institute' => 'teacher',
                ]
            );

            // 3) اربط المدرّس بالحلقة في pivot users_classes
            //    استخدم attach أو syncWithoutDetaching لتجنّب خطأ التكرار
            $cls->users()->syncWithoutDetaching($data['user_id']);

            // — بقية خطواتك (QR code مثلاً) …
            // إنشاء جداول المواعيد إذا وجدت
            if (!empty($data['schedules'])) {
                foreach ($data['schedules'] as $sch) {
                    $cls->sessionSchedules()->create($sch);
                }
            }
        });

        return redirect()
            ->route('manager.classes.index')
            ->with('success', 'تم إنشاء الحلقة وتحديد جداول المواعيد بنجاح');
    }


//    public function store(Request $request)
//    {
//        $inst = $this->currentInstitute();
//
//        $data = $request->validate([
//            'name'           => 'required|string|max:255',
//            'subject_id'     => 'required|exists:subjects,id',
//            'user_id'        => 'required|exists:users,id',
//            'students_count' => 'required|integer|min:1',
//        ]);
//
//        // 1) إنشاء الحلقة
//        $cls = EducationClass::create($data);
//
////        // 2) رابط صفحة تفاصيل الحلقة
////        $url = route('manager.classes.show', $cls->id);
////
////        // 3) توليد QR بصيغة SVG (pure PHP)
////        $renderer = new ImageRenderer(
////            new RendererStyle(200),
////            new SvgImageBackEnd()
////        );
////        $writer  = new Writer($renderer);
////        $svg     = $writer->writeString($url);
////
////        // 4) حفظ ملف الـ SVG في storage
////        Storage::put("public/qrcodes/class-{$cls->id}.svg", $svg);
////
////        // 5) تحديث مسار الـ QR في السجل
////        $cls->update([
////            'qr' => "qrcodes/class-{$cls->id}.svg",
////        ]);
//
//        return redirect()
//            ->route('manager.classes.index', $cls->id)
//            ->with('success', 'تم إنشاء الحلقة بنجاح');
//    }

    /**
     * عرض تفاصيل حلقة واحدة مع إحصائيات
     */
    public function show(EducationClass $class)
    {
        $inst = $this->currentInstitute();
        abort_if($class->subject->institute_id !== $inst->id, 403);
        $class->load(['teacher', 'subject', 'users', 'sessions', 'exams', 'progress', 'sessionSchedules']);


        $studentsCount = $class->users()
            ->where('users.role_id', '<>', 4)
            ->count();        $presentPercentage = $class->present_percentage;
        $sessionsHeld      = $class->sessions->where('start_time', '<=', now())->count();
        $totalPoints       = $class->exams->sum('points');
        $students = DB::table('users')
            ->join('users_classes','users.id','=','users_classes.user_id')
            ->join('students','students.user_id','=','users.id')
            ->where('users_classes.class_id', $class->id)
            ->select('users.id','students.first_name','students.last_name')
            ->get();
        return view('manager.classes.show', compact(
            'class',
            'studentsCount',
            'presentPercentage',
            'sessionsHeld',
            'totalPoints',
            'students'
        ));
    }

    /**
     * عرض نموذج تعديل حلقة
     */
    public function edit(EducationClass $class)
    {
        $inst = $this->currentInstitute();
        abort_if($class->subject->institute_id !== $inst->id, 403);

        // جلب العلاقة sessionSchedules حتى لا تكون null
        $class->load(['subject', 'teacher', 'sessionSchedules']);

        // جلب المواد والمدرّسين كما قبل
        $subjects = Subject::where('institute_id', $inst->id)->get();


        // نفس جلب الـ teachers كما في create()
        $teachers = DB::table('institute_user')
            ->join('teachers','institute_user.user_id','=','teachers.user_id')
            ->where('institute_user.institute_id',$inst->id)
            ->where('institute_user.role_institute','teacher')
            ->select([
                'teachers.user_id as id',
                'teachers.first_name',
                'teachers.last_name',
            ])
            ->get();

        return view('manager.classes.edit', compact('class','subjects','teachers'));
    }

    /**
     * حفظ تعديل حلقة
     */
    public function update(Request $request, EducationClass $class)
    {
        $inst = $this->currentInstitute();
        abort_if($class->subject->institute_id !== $inst->id, 403);

        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'subject_id'     => 'required|exists:subjects,id',
            'user_id'        => 'required|exists:users,id',
            'students_count' => 'required|integer|min:1',
        ]);

        DB::transaction(function() use ($class, $data){
            $class->update($data);

            // نجمع الـ IDs الموجودة لتحاشي حذفها
            $keep = [];

            if (!empty($data['schedules'])) {
                foreach ($data['schedules'] as $sch) {
                    if (!empty($sch['id'])) {
                        // تحديث الجدول الحالي
                        $schedule = $class->sessionSchedules()->findOrFail($sch['id']);
                        $schedule->update($sch);
                        $keep[] = $schedule->id;
                    } else {
                        // إنشاء جديد
                        $new = $class->sessionSchedules()->create($sch);
                        $keep[] = $new->id;
                    }
                }
            }

            // حذف أي جداول لم تعد موجودة بالطلب
            $class->sessionSchedules()
                ->whereNotIn('id', $keep)
                ->delete();
        });

        return back()->with('success', 'تم حفظ التعديلات على الحلقة وجداول المواعيد');
    }
    /**
     * حذف/تعطيل حلقة
     */
    public function destroy(EducationClass $class)
    {
        $inst = $this->currentInstitute();
        abort_if($class->subject->institute_id !== $inst->id, 403);

        $class->delete();

        return redirect()
            ->route('manager.classes.index')
            ->with('success', 'تم حذف الحلقة');
    }
}
