{{-- resources/views/admin/students/index.blade.php --}}
@extends('layouts.app')
@section('title','إدارة الطلاب')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">إدارة الطلاب</h1>
            <a href="{{ route('admin.students.create', request()->only('institute_id')) }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> إضافة طالب جديد
            </a>
        </div>

        {{-- Card للفلاتر --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <form method="GET" action="{{ route('admin.students.index') }}" class="row g-3 align-items-end">
                    {{-- الإبقاء على اختيار المعهد إن تم تمريره --}}
                    @if(request('institute_id'))
                        <input type="hidden" name="institute_id" value="{{ request('institute_id') }}">
                    @endif

                    {{-- مادة --}}
                    <div class="col-md-2">
                        <label class="form-label">المادة</label>
                        <select name="subject_id" class="form-select form-select-sm">
                            <option value="">الكل</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}" @selected(request('subject_id') == $sub->id)>{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- حلقة --}}
                    <div class="col-md-2">
                        <label class="form-label">الحلقة</label>
                        <select name="class_id" class="form-select form-select-sm">
                            <option value="">الكل</option>
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}" @selected(request('class_id') == $cls->id)>{{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- مستوى --}}
                    <div class="col-md-2">
                        <label class="form-label">المستوى</label>
                        <select name="level" class="form-select form-select-sm">
                            <option value="">الكل</option>
                            @foreach($levels as $lvl)
                                <option value="{{ $lvl }}" @selected(request('level') == $lvl)>{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- نسبة حضور ≥ --}}
                    <div class="col-md-2">
                        <label class="form-label">حضور ≥ (%)</label>
                        <input type="number" name="attendance_min" value="{{ request('attendance_min') }}"
                               min="0" max="100" class="form-control form-control-sm">
                    </div>

                    {{-- علامة تسميع ≥ --}}
                    <div class="col-md-2">
                        <label class="form-label">تسميع ≥</label>
                        <input type="number" name="min_score" value="{{ request('min_score') }}"
                               min="0" class="form-control form-control-sm">
                    </div>

                    {{-- زر تطبيق / إعادة ضبط --}}
                    <div class="col-md-2 text-end">
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-funnel-fill"></i> تطبيق
                        </button>
                        <a href="{{ route('admin.students.index', request()->only('institute_id')) }}"
                           class="btn btn-outline-secondary btn-sm w-100 mt-1">
                            إعادة ضبط
                        </a>
                    </div>
                </form>
            </div>

            {{-- جدول النتائج --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 text-end">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>المادة</th>
                            <th>الحلقة</th>
                            <th>نسبة الحضور</th>
                            <th>أقصى تسميع</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $loop->iteration + ($students->currentPage()-1)*$students->perPage() }}</td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ optional($student->classes->first()?->subject)->name ?? '—' }}</td>
                                <td>{{ optional($student->classes->first())->name ?? '—' }}</td>
                                <td>{{ $student->present_percentage }}%</td>
                                <td>{{ $student->exams->pluck('score')->max() ?? '—' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.students.show',$student) }}"
                                       class="btn btn-sm btn-outline-info" title="عرض">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('admin.students.edit',$student) }}"
                                       class="btn btn-sm btn-outline-warning" title="تعديل">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.students.destroy',$student) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('هل تريد حذف الطالب؟');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">لا توجد نتائج</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($students->hasPages())
                <div class="card-footer">
                    {{ $students->appends(request()->only('institute_id'))->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
