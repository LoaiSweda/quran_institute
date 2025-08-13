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
                {{-- عرض فقط للمشرف --}}
                <a href="{{ route('admin.teachers.index') }}"
                   class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> رجوع
                </a>
            </div>
        </div>

        {{-- بطاقات إحصائية --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-4">
                <div class="card text-white bg-primary h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">عدد الحلقات المسندة</h5>
                        <p class="display-6">{{ $stats['classes_count'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card text-white bg-success h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">مجموع الطلاب</h5>
                        <p class="display-6">{{ $stats['students_sum'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card text-white bg-info h-100 shadow-sm">
                    <div class="card-body text-end">
                        <h5 class="card-title mb-1">مجموع الجلسات</h5>
                        <p class="display-6">{{ $stats['sessions_sum'] ?? 0 }}</p>
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
                    <p><strong>المستخدم المرتبط:</strong>
                        {{ $teacher->user->name ?? '—' }}
                        ({{ $teacher->user->email ?? '—' }})
                    </p>
                    <p><strong>الهاتف:</strong> {{ $teacher->phone ?? '—' }}</p>
                    <p><strong>العنوان:</strong> {{ $teacher->address ?? '—' }}</p>
                    <p><strong>تاريخ الميلاد:</strong>
                        {{ $teacher->birthdate?->format('Y-m-d') ?? '—' }}
                    </p>
                    <p><strong>المعاهد:</strong>
                        @if($teacher->institutes && $teacher->institutes->count())
                            {{ $teacher->institutes->pluck('name')->join('، ') }}
                        @else
                            —
                        @endif
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
                    $assigned = $teacher->teachingClasses ?? collect();
                @endphp

                @if($assigned->isEmpty())
                    <p class="text-center text-muted py-4">لا توجد حلقات مسندة.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 text-end">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الحلقة</th>
                                <th>المادة</th>
                                <th>عدد الطلاب</th>
                                <th>عدد الجلسات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($assigned as $class)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $class->name }}</td>
                                    <td>{{ $class->subject->name ?? '—' }}</td>
                                    <td>{{ $class->students_count ?? 0 }}</td>
                                    <td>{{ $class->session_count ?? 0 }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- الجدول الأسبوعي (مجمّع من حلقات التدريس) --}}
        @php
            $slots = $teacher->teachingClasses
                ? $teacher->teachingClasses
                    ->loadMissing('sessionSchedules', 'subject')
                    ->flatMap(fn($cls) => $cls->sessionSchedules->map(function($s) use ($cls){
                        $s->class_name  = $cls->name;
                        $s->subject_name= $cls->subject->name ?? '—';
                        return $s;
                    }))
                    ->sortBy(fn($s) => [$s->day_of_week, $s->start_time])
                : collect();
        @endphp

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">الجدول الأسبوعي</h5>
            </div>
            <div class="card-body p-0">
                @if($slots->isEmpty())
                    <p class="text-center text-muted py-4">لا يوجد جدول.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-end">
                            <thead class="table-light">
                            <tr>
                                <th>الحلقة</th>
                                <th>المادة</th>
                                <th>اليوم</th>
                                <th>من</th>
                                <th>إلى</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($slots as $slot)
                                <tr>
                                    <td>{{ $slot->class_name }}</td>
                                    <td>{{ $slot->subject_name }}</td>
                                    <td>{{ $slot->day_of_week }}</td>
                                    <td>{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
