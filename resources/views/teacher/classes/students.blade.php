{{-- resources/views/teacher/classes/students.blade.php --}}
@extends('layouts.app')

@section('title', "طلاب الحلقة: {$class->name}")

@section('content')
<div class="container-fluid">

    {{-- العنوان وزر العودة --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">طلاب الحلقة: {{ $class->name }}</h1>
        <a href="{{ route('teacher.classes.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> رجوع للحلقات
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                @if($students->isEmpty())
                    <p class="text-center text-muted py-4 mb-0">لا يوجد طلاب في هذه الحلقة.</p>
                @else
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الهاتف</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $idx => $student)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        {{ $student->student->first_name ?? '' }}
                                        {{ $student->student->last_name  ?? '' }}
                                    </td>
                                    <td>{{ $student->student->phone ?? '-' }}</td>
                                    <td class="text-center">
                                        {{-- عرض التفاصيل --}}
                                        <a href="{{ route('teacher.students.show', $student) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="عرض التفاصيل">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        {{-- إضافة امتحان --}}
                                        <a href="{{ route('teacher.classes.students.exams.create', [
                                                        'class'   => $class->id,
                                                        'student' => $student->id
                                                    ]) }}"
                                           class="btn btn-sm btn-outline-success"
                                           title="إضافة امتحان">
                                            <i class="bi bi-journal-plus"></i>
                                        </a>

                                        {{-- إزالة من الحلقة --}}
                                        <form action="{{ route('teacher.classes.students.remove', [
                                                            'class'   => $class->id,
                                                            'student' => $student->id
                                                        ]) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من إزالة هذا الطالب من الحلقة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="إزالة الطالب">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
