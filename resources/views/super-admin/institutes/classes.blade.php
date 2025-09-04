@extends('layouts.app')

@section('title', 'فصول المعهد - ' . $institute->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header bg-white p-4 rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 text-primary">فصول المعهد: {{ $institute->name }}</h2>
                            <p class="text-muted mb-0"> جميع فصول المعهد</p>
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
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-door-open me-2"></i>قائمة الفصول ({{ $classes->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($classes->isEmpty())
                            <p class="text-center text-muted p-4">لا توجد فصول دراسية في هذا المعهد</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم الفصل</th>
                                        <th>المادة</th>
                                        <th>المعلم</th>
                                        <th>عدد الطلاب</th>
                                        <th>عدد الجلسات</th>
                                        <th>نسبة الحضور</th>
                                        <th>الحالة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($classes as $class)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $class->name }}</td>
                                            <td>{{ $class->subject->name ?? 'غير محدد' }}</td>
                                            <td>
                                                @if($class->teacher && $class->teacher->user)
                                                    {{ $class->teacher->user->first_name }} {{ $class->teacher->user->last_name }}
                                                @else
                                                    غير معين
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $class->students->count() }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $class->sessionSchedules->count() }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $attendanceRate = $class->students->avg('present_percentage') ?? 0;
                                                @endphp
                                                <div class="progress" style="height: 10px; width: 100px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                         style="width: {{ $attendanceRate }}%;"
                                                         aria-valuenow="{{ $attendanceRate }}"
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small>{{ $attendanceRate }}%</small>
                                            </td>
                                            <td>
                                                    <span class="badge bg-{{ $class->is_active ? 'success' : 'secondary' }}">
                                                        {{ $class->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
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
