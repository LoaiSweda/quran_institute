{{-- resources/views/manager/students/show.blade.php --}}
@extends('layouts.app')
@section('title',"تفاصيل الطالب — {$student->first_name} {$student->last_name}")

@section('content')
    <div class="container-fluid">
        {{-- Header + Back --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">تفاصيل الطالب</h1>
            <a href="{{ route('manager.students.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> رجوع
            </a>
        </div>

        {{-- الإحصائيات --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-primary h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">نسبة الحضور</h5>
                        <p class="display-6">{{ $student->present_percentage }}%</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-success h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">النقاط</h5>
                        <p class="display-6">{{ $student->points }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-info h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">عدد المواد</h5>
                        <p class="display-6">
                            {{ optional($student->classes)
                                 ->pluck('subject.name')
                                 ->unique()
                                 ->count() }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-warning h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">عدد الحلقات</h5>
                        <p class="display-6">{{ optional($student->classes)->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- المعلومات الأساسية --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">المعلومات الأساسية</h5>
            </div>
            <div class="card-body text-end">
                <p><strong>الاسم:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
                <p><strong>كود QR:</strong> {{ $student->qr }}</p>
                <p><strong>البريد الإلكتروني:</strong> {{ $student->user->email }}</p>
                <p><strong>تاريخ الميلاد:</strong>
                    {{
                      $student->birthdate
                        ? \Illuminate\Support\Carbon::parse($student->birthdate)->format('Y-m-d')
                        : '—'
                    }}
                </p>
                <p><strong>الهاتف:</strong> {{ $student->phone ?: '—' }}</p>
                <p><strong>العنوان:</strong> {{ $student->address ?: '—' }}</p>
                <p><strong>اسم الأب:</strong> {{ $student->father_name ?: '—' }}</p>
                <p><strong>الوصي:</strong> {{ optional($student->guardian)->name ?: '—' }}</p>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('manager.students.edit',$student) }}" class="btn btn-warning">
                    <i class="bi bi-pencil-square"></i> تعديل
                </a>
                <form action="{{ route('manager.students.destroy',$student) }}"
                      method="POST" class="d-inline"
                      onsubmit="return confirm('هل تريد حذف الطالب؟');">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger">
                        <i class="bi bi-trash"></i> حذف
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
