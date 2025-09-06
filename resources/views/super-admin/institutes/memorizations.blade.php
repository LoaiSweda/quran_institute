@extends('layouts.app')

@section('title', 'تسميعات المعهد - ' . $institute->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header bg-white p-4 rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 text-primary">تسميعات المعهد: {{ $institute->name }}</h2>
                            <p class="text-muted mb-0">عرض جميع سجل التسميعات في المعهد</p>
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
                    <div class="card-header bg-purple text-white">
                        <h5 class="mb-0"><i class="fas fa-quran me-2"></i>قائمة التسميعات ({{ $memorizations->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($memorizations->isEmpty())
                            <p class="text-center text-muted p-4">لا توجد تسميعات مسجلة في هذا المعهد</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>الطالب</th>
                                        <th>المادة</th>
                                        <th>عدد الجلسات الحاضرة</th>
                                        <th>معدل الدرجات</th>
                                        <th>معدل الملاحظة</th>
                                        <th>النقاط الإجمالية</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($memorizations as $memorization)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $memorization->student->user->name ?? 'غير محدد' }}</td>
                                            <td>{{ $memorization->educationClass->subject->name ?? 'غير محدد' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $memorization->number_sessions_attended }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $memorization->degree_avg }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">{{ $memorization->eohservation_rate }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ $memorization->total_points_subject }}</span>
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
