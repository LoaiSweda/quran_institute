{{-- resources/views/manager/classes/show.blade.php --}}
@extends('layouts.app')
@section('title',"تفاصيل الحلقة — {$class->name}")

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800">تفاصيل الحلقة</h1>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('manager.classes.students.index', $class) }}"
               class="btn btn-outline-primary btn-sm">
                <i class="bi bi-people"></i> إدارة طلاب الحلقة
            </a>
            <a href="{{ route('manager.classes.edit', $class) }}"
               class="btn btn-warning btn-sm">
                <i class="bi bi-pencil-square"></i> تعديل
            </a>
            <a href="{{ route('manager.classes.index') }}"
               class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> رجوع
            </a>
        </div>
    </div>

        {{-- الإحصائيات --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-primary h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">عدد الطلاب</h5>
                        <p class="display-6">{{ $studentsCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-success h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">نسبة الحضور</h5>
                        <p class="display-6">{{ $presentPercentage }}%</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-info h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">الجلسات المنعقدة</h5>
                        <p class="display-6">{{ $sessionsHeld }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-warning h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">مجموع النقاط</h5>
                        <p class="display-6">{{ $totalPoints }}</p>
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
                <p><strong>اسم الحلقة:</strong> {{ $class->name }}</p>
                <p><strong>المادة:</strong> {{ optional($class->subject)->name ?: '—' }}</p>
                <p><strong>المدرّس:</strong>
                    {{ optional($class->teacher)->first_name }}
                    {{ optional($class->teacher)->last_name }}
                    @if(optional($class->teacher)->email)
                        ({{ $class->teacher->email }})
                    @endif
                </p>
                <p><strong>عدد الطلاب:</strong> {{ $studentsCount }}</p>
                <p><strong>عدد الجلسات:</strong> {{ $class->sessions->count() }}</p>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('manager.classes.edit', $class) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil-square"></i> تعديل
                </a>
            </div>
        </div>

        {{-- QR Code --}}
        @if($class->qr)
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">QR Code للحلقة</h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/' . $class->qr) }}"
                         alt="QR Code" class="img-fluid" style="max-width:200px">
                </div>
            </div>
        @endif

        {{-- جدول الجلسات --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">جدول الجلسات</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0 text-end">
                    <thead class="table-light">
                    <tr>
                        <th>اليوم</th>
                        <th>من</th>
                        <th>إلى</th>
                        <th>حضور</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($class->sessions as $sch)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($sch->start_time)->translatedFormat('l') }}</td>
                            <td>{{ \Carbon\Carbon::parse($sch->start_time)->format('H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($sch->end_time)->format('H:i') }}</td>
                            <td>{{ $sch->users()->count() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">لا توجد جلسات</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @php
        // نصفي المستخدمين لاستثناء role_id = 4
        $students = $class->users->where('role_id', '<>', 4);
    @endphp

    {{-- جدول طلاب الحلقة --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">طلاب الحلقة</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0 text-end">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>نسبة الحضور</th>
                    <th>معدل الدرجات</th>
                    <th>الجلسات المحضورة</th>
                    <th>مجموع النقاط</th>
                    <th class="text-center">إجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($students as $stu)
                    @php
                        $prog = $class->progress->firstWhere('student_id', $stu->id);
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $stu->first_name }} {{ $stu->last_name }}</td>
                        <td>{{ $prog->observation_rate ?? '—' }}%</td>
                        <td>{{ $prog->degree_avg ?? '—' }}</td>
                        <td>{{ $prog->number_sessions_attended ?? '—' }}</td>
                        <td>{{ $prog->total_points_subject ?? '—' }}</td>
                        <td class="text-center">
                            <a href="{{ route('manager.students.show', $stu) }}"
                               class="btn btn-sm btn-outline-info" title="عرض طالب">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">لا توجد طلاب</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- جدول الاختبارات --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">التسميعات والاختبارات</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0 text-end">
                    <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>اسم الاختبار</th>
                        <th>الطالب</th>
                        <th>النقاط</th>
                        <th>درجة الاختبار</th>
                        <th>التاريخ</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($class->exams as $ex)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $ex->name }}</td>
                            <td>{{ $ex->student->first_name }} {{ $ex->student->last_name }}</td>
                            <td>{{ $ex->points }}</td>
                            <td>{{ $ex->degree }}</td>
                            <td>{{ $ex->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">لا توجد اختبارات</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>


    </div>
@endsection
