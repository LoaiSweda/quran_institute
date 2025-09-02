{{-- resources/views/teacher/classes/show.blade.php --}}
@extends('layouts.app')

@section('title', "تفاصيل الحلقة: {$class->name}")

@section('content')
<div class="container-fluid">

    {{-- العنوان وزر العودة --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">حلقة: {{ $class->name }}</h1>
        <a href="{{ route('teacher.classes.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> رجوع للحلقات
        </a>
    </div>

    {{-- البطاقات الإحصائية --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-people-fill fs-1 text-primary mb-2"></i>
                    <h5 class="card-title">عدد الطلاب</h5>
                    <p class="card-text fs-4">{{ $class->users->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-clock-fill fs-1 text-success mb-2"></i>
                    <h5 class="card-title">جلسات</h5>
                    <p class="card-text fs-4">{{ $class->sessions->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-bar-chart-fill fs-1 text-warning mb-2"></i>
                    <h5 class="card-title">نسبة الحضور</h5>
                    <p class="card-text fs-4">{{ $class->present_percentage }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-qr-code fs-1 text-danger mb-2"></i>
                    <h5 class="card-title">رمز QR</h5>
                    <p class="card-text fs-4">{{ $class->qr }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول الجلسات --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0"><i class="bi bi-calendar-event-fill"></i> جدول الجلسات</h5>
        </div>
        <div class="card-body p-0">
            @if($class->sessions->isEmpty())
                <p class="text-center text-muted py-3 mb-0">لا توجد جلسات مسجّلة.</p>
            @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>اليوم</th>
                                <th>من</th>
                                <th>إلى</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($class->sessions as $sess)
                                <tr>
                                    @php
                                        $dayMapEnToAr = [
                                            'Saturday'  => 'السبت',
                                            'Sunday'    => 'الأحد',
                                            'Monday'    => 'الاثنين',
                                            'Tuesday'   => 'الثلاثاء',
                                            'Wednesday' => 'الأربعاء',
                                            'Thursday'  => 'الخميس',
                                            'Friday'    => 'الجمعة',
                                        ];
                                    @endphp

                                    <td>{{ $dayMapEnToAr[$sess->day_of_week] ?? $sess->day_of_week }}</td>

                                    <td>{{ \Carbon\Carbon::parse($sess->start_time)->format('H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sess->end_time)->format('H:i') }}</td>
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
