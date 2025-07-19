<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    /**
     * Display a listing of the manager’s own subjects,
     * with search, sort, and pagination.
     */
    public function index(Request $request)
    {
        // 1) Grab the institute of the logged-in user
        $institute = auth()->user()->institute;
        if (! $institute) {
            abort(403, 'لا يوجد معهد مرتبط بالمستخدم الحالي.');
        }

        // 2) Build the query scoped to this institute
        $query = Subject::where('institute_id', $institute->id);

        // 2a) Search by name or description
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 2b) Sort if requested
        if ($sort = $request->input('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }

        // 3) Paginate
        $subjects = $query->paginate(10)->withQueryString();

        // 4) Return index view
        return view('manager.subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new subject.
     */
    // app/Http/Controllers/Manager/SubjectsController.php

    public function create(Request $request)
    {
        // 1) جلب معهد المدير
        $institute = auth()->user()->institute;
        if (! $institute) {
            abort(403, 'لا يوجد معهد مرتبط بالمستخدم الحالي.');
        }

        // 2) بناء استعلام المواد لمعرض الإنشاء
        $query = Subject::where('institute_id', $institute->id);

        // (اختياري) قم بنفس بحث/فرز GET params إن أردت:
        if ($search = $request->input('search')) {
            $query->where(fn($q) =>
            $q->where('name','like',"%{$search}%")
                ->orWhere('description','like',"%{$search}%")
            );
        }
        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction','asc'));
        } else {
            $query->orderBy('created_at','desc');
        }

        // 3) جلب صفحة أولى أو كلها إن أردت
        $subjects = $query->paginate(10)->withQueryString();

        // 4) عرض الفورم + القائمة معاً
        return view('manager.subjects.create', compact('subjects'));
    }

    /**
     * Store a newly created subject in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'level'          => 'nullable|string|max:100',
            'degree'         => 'nullable|numeric|min:0',
            'exams_count'    => 'nullable|integer|min:0',    // ← أضف هذا

            'total_sessions' => 'nullable|integer|min:0',
            'is_active'      => 'nullable|boolean',
        ]);

        $institute = auth()->user()->institute;
        if (! $institute) {
            return back()
                ->withInput()
                ->withErrors(['institute_id' => 'لا يوجد معهد مرتبط بالمستخدم الحالي.']);
        }

        $data['institute_id'] = $institute->id;
        Subject::create($data);

        return redirect()
            ->route('manager.subjects.index')
            ->with('success','تم إضافة المادة بنجاح.');
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject)
    {
        return view('manager.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject)
    {
        return view('manager.subjects.edit', compact('subject'));
    }

    /**
     * Update the specified subject in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'level'          => 'nullable|string|max:100',
            'degree'         => 'nullable|numeric|min:0',
            'exams_count'    => 'nullable|integer|min:0',    // ← أضف هذا

            'total_sessions' => 'nullable|integer|min:0',
            'is_active'      => 'nullable|boolean',
        ]);

        $subject->update($data);

        return redirect()
            ->route('manager.subjects.index')
            ->with('success','تم تحديث المادة بنجاح.');
    }

    /**
     * Remove the specified subject from storage.
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()
            ->route('manager.subjects.index')
            ->with('success','تم حذف المادة بنجاح.');
    }
    public function toggle(Subject $subject)
    {
        // فقط لمواد هذا المعهد!
        if ($subject->institute_id !== auth()->user()->institute->id) {
            abort(403);
        }
        $subject->update(['is_active' => ! $subject->is_active]);
        return back()->with('success','تم تغيير الحالة.');
    }

}
