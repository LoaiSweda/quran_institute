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
    {{-- جدول حلقات الطالب --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">الحلقات المشتركة للطالب</h5>
        </div>
        <div class="card-body p-0">
            @if($student->classes->isEmpty())
                <p class="text-center text-muted py-4">لم يشترك الطالب في أية حلقات.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped mb-0 text-end">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>اسم الحلقة</th>
                            <th>المادة</th>
                            <th>المدرِّس</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($student->classes as $idx => $class)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $class->name }}</td>
                                <td>{{ optional($class->subject)->name ?: '—' }}</td>
                                <td>
                                    {{ optional($class->teacher)->first_name }}
                                    {{ optional($class->teacher)->last_name }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('manager.classes.show', $class) }}"
                                       class="btn btn-sm btn-outline-info" title="عرض الحلقة">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    {{-- جدول مواعيد الطالب الأسبوعي --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">جدول توقيت المواد للطالب</h5>
        </div>
        <div class="card-body p-0">
            @php
                // نجمع كل المواعيد من حلقات الطالب
                $slots = $student->classes
                    ->flatMap(function($cls) {
                        return $cls->sessionSchedules->map(fn($s) => [
                            'class' => $cls,
                            'slot'  => $s,
                        ]);
                    })
                    ->sortBy(fn($item) => [$item['slot']->day_of_week, $item['slot']->start_time]);
            @endphp

            @if($slots->isEmpty())
                <p class="text-center text-muted py-4">لا يوجد جدول مواعيد.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped mb-0 text-end">
                        <thead class="table-light">
                        <tr>
                            <th>اليوم</th>
                            <th>من</th>
                            <th>إلى</th>
                            <th>اسم الحلقة</th>
                            <th>المادة</th>
                            <th>المدرِّس</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($slots as $item)
                            @php
                                $cls  = $item['class'];
                                $sch  = $item['slot'];
                                $day  = \Carbon\Carbon::parse($sch->start_time)->translatedFormat('l');
                                $from = \Carbon\Carbon::parse($sch->start_time)->format('H:i');
                                $to   = \Carbon\Carbon::parse($sch->end_time)->format('H:i');
                            @endphp
                            <tr>
                                <td>{{ $day }}</td>
                                <td>{{ $from }}</td>
                                <td>{{ $to }}</td>
                                <td>{{ $cls->name }}</td>
                                <td>{{ optional($cls->subject)->name }}</td>
                                <td>
                                    {{ optional($cls->teacher)->first_name }}
                                    {{ optional($cls->teacher)->last_name }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>


@endsection
