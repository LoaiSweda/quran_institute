@extends('layouts.app')

@section('title', 'امتحانات المعهد - ' . $institute->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header bg-white p-4 rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 text-primary">امتحانات المعهد: {{ $institute->name }}</h2>
                            <p class="text-muted mb-0">عرض جميع امتحانات المعهد</p>
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
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>قائمة الامتحانات ({{ $exams->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($exams->isEmpty())
                            <p class="text-center text-muted p-4">لا توجد امتحانات في هذا المعهد</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم الامتحان</th>
                                        <th>المادة</th>
                                        <th>الطالب</th>
                                        <th>النقاط</th>
                                        <th>الدرجة</th>
                                        <th>التاريخ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($exams as $exam)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $exam->name }}</td>
                                            <td>{{ $exam->educationClass->subject->name ?? 'غير محدد' }}</td>
                                            <td>{{ $exam->student->user->name ?? 'غير محدد' }}</td>
                                            <td>
                                                <span class="badge bg-warning">{{ $exam->points }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $exam->degree }}</span>
                                            </td>
                                            <td>{{ $exam->created_at->format('Y-m-d') }}</td>

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
