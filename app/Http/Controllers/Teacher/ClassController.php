<?php
// app/Http/Controllers/Teacher/ClassController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = auth()->id();

        $query = EducationClass::where('user_id', $teacherId);

        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $classes = $query->orderBy('id','desc')
                         ->paginate(10)
                         ->withQueryString();

        return view('teacher.classes.index', compact('classes'));
    }

    public function show(EducationClass $class)
    {
        if ($class->user_id !== auth()->id()) {
            abort(403);
        }
        $class->load('users','exams');
        return view('teacher.classes.show', compact('class'));
    }

    public function students(\App\Models\EducationClass $class)
    {
        if ($class->user_id !== auth()->id()) {
            abort(403);
        }

        $students = $class->users()->with('student')->get();

        return view('teacher.classes.students', compact('class', 'students'));
    }
}
