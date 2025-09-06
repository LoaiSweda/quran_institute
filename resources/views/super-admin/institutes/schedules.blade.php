@extends('layouts.app')

@section('title', 'جداول المعهد - ' . $institute->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header bg-white p-4 rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 text-primary">جداول المعهد: {{ $institute->name }}</h2>
                            <p class="text-muted mb-0">عرض جميع جداول حصص المعهد</p>
                        </div>
                        <div>
                            <a href="{{ route('super-admin.institutes.Details', $institute) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-2"></i> العودة للتفاصيل
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-indigo text-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>قائمة الجداول ({{ $schedules->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($schedules->isEmpty())
                            <p class="text-center text-muted p-4">لا توجد جداول في هذا المعهد</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>اليوم</th>
                                        <th>وقت البدء</th>
                                        <th>وقت الانتهاء</th>
                                        <th>المادة</th>
                                        <th>الفصل</th>
                                        <th>المعلم</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($schedules as $schedule)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @php
                                                    $days = [
                                                        'Saturday' => 'السبت',
                                                        'Sunday' => 'الأحد',
                                                        'Monday' => 'الإثنين',
                                                        'Tuesday' => 'الثلاثاء',
                                                        'Wednesday' => 'الأربعاء',
                                                        'Thursday' => 'الخميس',
                                                        'Friday' => 'الجمعة'
                                                    ];
                                                @endphp
                                                {{ $days[$schedule->day_of_week] ?? $schedule->day_of_week }}
                                            </td>
                                            <td>{{ $schedule->start_time->format('H:i') }}</td>
                                            <td>{{ $schedule->end_time->format('H:i') }}</td>
                                            <td>{{ $schedule->educationClass->subject->name ?? 'غير محدد' }}</td>
                                            <td>{{ $schedule->educationClass->name ?? 'غير محدد' }}</td>
                                            <td>
                                                @if($schedule->educationClass->teacher && $schedule->educationClass->teacher->user)
                                                    {{ $schedule->educationClass->teacher->user->first_name }} {{ $schedule->educationClass->teacher->user->last_name }}
                                                @else
                                                    غير معين
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
