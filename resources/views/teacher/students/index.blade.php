{{-- resources/views/teacher/students/index.blade.php --}}
@extends('layouts.app')

@section('title','جميع طلابي')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">جميع طلابي</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <form method="GET" action="{{ route('teacher.students.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input
                        type="text"
                        name="search"
                        class="form-control form-control-sm"
                        placeholder="🔍 بحث بالاسم أو الهاتف..."
                        value="{{ request('search') }}"
                    >
                </div>
                <div class="col-md-2 text-md-end">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-funnel-fill"></i> تطبيق
                    </button>
                    <a href="{{ route('teacher.students.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> إعادة ضبط
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                @if($students->isEmpty())
                    <p class="text-center text-muted py-4 mb-0">لا يوجد طلاب لعرضهم.</p>
                @else
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الهاتف</th>
                                <th>الصف</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $idx => $student)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        {{ $student->student->first_name ?? '' }}
                                        {{ $student->student->last_name ?? '' }}
                                    </td>

                                    <td>
                                        {{ $student->student->phone ?? '-' }}
                                    </td>
                                    <td>
                                        @foreach($classes->filter(fn($c) => $c->users->contains($student)) as $class)
                                            <span class="badge bg-secondary">{{ $class->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        {{-- زرّ عرض التفاصيل --}}
                                        <a href="{{ route('teacher.students.show', $student) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="عرض التفاصيل">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        {{-- زرّ إضافة امتحان --}}
                                        @foreach($classes->filter(fn($c) => $c->users->contains($student)) as $class)
                                            <a href="{{ route('teacher.classes.students.exams.create', ['class'=>$class->id,'student'=>$student->id]) }}"
                                            class="btn btn-sm btn-outline-success"
                                            title="إضافة امتحان في صف {{ $class->name }}">
                                                <i class="bi bi-journal-plus"></i>
                                            </a>
                                        @endforeach

                                        {{-- نموذج حقل الحذف (إزالة الطالب من الصف) --}}
                                        <form action="{{ route('teacher.classes.students.remove', [
                                                            'class'   => $class->id,
                                                            'student' => $student->id
                                                        ]) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('هل أنت متأكد من إزالة هذا الطالب من الصف؟');">
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
