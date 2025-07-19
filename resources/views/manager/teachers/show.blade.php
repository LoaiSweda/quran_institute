{{-- resources/views/manager/teachers/show.blade.php --}}
@extends('layouts.app')
@section('title', "تفاصيل المدرس — {$teacher->first_name} {$teacher->last_name}")

@section('content')
    <div class="container-fluid">

        {{-- العنوان وأزرار الإجراء --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">
                تفاصيل المدرس: {{ $teacher->first_name }} {{ $teacher->last_name }}
            </h1>
            <div>
                <a href="{{ route('manager.teachers.edit', $teacher) }}"
                   class="btn btn-warning btn-sm me-2">
                    <i class="bi bi-pencil-square"></i> تعديل
                </a>
                <a href="{{ route('manager.teachers.index') }}"
                   class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> رجوع
                </a>
            </div>
        </div>

        {{-- بطاقات إحصائية --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-primary h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">عدد الحلقات المسندة</h5>
                        <p class="display-6">{{ $teacher->classes?->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-success h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">نسبة الحضور</h5>
                        <p class="display-6">{{ number_format($teacher->attendance_rate ?? 0, 2) }}%</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-info h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">تاريخ الإضافة</h5>
                        <p class="display-6">{{ $teacher->created_at?->format('Y-m-d') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-secondary h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">الحالة</h5>
                        <p class="display-6">
                            @if($teacher->institutes->contains(fn($i) => $i->id === auth()->user()->institute->id))
                                <span class="badge bg-success">مفعل</span>
                            @else
                                <span class="badge bg-danger">موقوف</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- معلومات أساسية --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">معلومات أساسية</h5>
            </div>
            <div class="card-body text-end row gy-3">
                <div class="col-md-4 text-center">
                    @if($teacher->image)
                        <img src="{{ asset('storage/'.$teacher->image) }}"
                             alt="صورة المدرس"
                             class="rounded-circle shadow-sm"
                             style="width:120px;height:120px;object-fit:cover;">
                    @else
                        <div class="text-muted">لا توجد صورة</div>
                    @endif
                </div>
                <div class="col-md-8">
                    <p><strong>الاسم الكامل:</strong> {{ $teacher->first_name }} {{ $teacher->last_name }}</p>
                    <p><strong>المستخدم المرتبط:</strong> {{ $teacher->user->name }} ({{ $teacher->user->email }})</p>
                    <p><strong>الهاتف:</strong> {{ $teacher->phone ?? '—' }}</p>
                    <p><strong>العنوان:</strong> {{ $teacher->address ?? '—' }}</p>
                    <p><strong>تاريخ الميلاد:</strong>
                        {{ $teacher->birthdate
                            ? $teacher->birthdate->format('Y-m-d')
                            : '—'
                        }}
                    </p>
                </div>
            </div>
        </div>

        {{-- الحلقات المسندة --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">الحلقات المسندة</h5>
            </div>
            <div class="card-body p-0">
                @php
                    $assigned = $teacher->teachingClasses;
                @endphp

                @if($assigned->isEmpty())
                    <p class="text-center text-muted py-4">لا توجد حلقات مسندة.</p>
                @else
                    <ul class="list-group list-group-flush text-end">
                        @foreach($assigned as $class)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>
              <strong>{{ $class->name }}</strong>
              <small class="text-muted">({{ $class->level }})</small>
            </span>
                                <span class="badge bg-primary">{{ $class->subject->name ?? '—' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        @php
            // نجمع كل الجداول من حلقات التدريس فقط
            $slots = $teacher->teachingClasses
                ->flatMap(fn($cls) => $cls->sessionSchedules)
                ->sortBy(fn($s) => [$s->day_of_week, $s->start_time]);
        @endphp

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">الجدول الأسبوعي</h5>
            </div>
            <div class="card-body p-0">
                @if($slots->isEmpty())
                    <p class="text-center text-muted py-4">لا يوجد جدول.</p>
                @else
                    <table class="table table-hover mb-0 text-end">
                        <thead class="table-light">
                        <tr>
                            <th>الحلقة</th>
                            <th>اليوم</th>
                            <th>من</th>
                            <th>إلى</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($slots as $slot)
                            <tr>
                                <td>{{ $slot->educationClass->name }}</td>
                                <td>{{ $slot->day_of_week }}</td>
                                <td>{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection




