@extends('layouts.app')

@section('title', 'مواد المعهد - ' . $institute->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header bg-white p-4 rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 text-primary">مواد المعهد: {{ $institute->name }}</h2>
                            <p class="text-muted mb-0"> جميع مواد المعهد</p>
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
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-book me-2"></i>قائمة المواد ({{ $subjects->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($subjects->isEmpty())
                            <p class="text-center text-muted p-4">لا توجد مواد في هذا المعهد</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم المادة</th>
                                        <th>الوصف</th>
                                        <th>عدد الفصول</th>
                                        <th>عدد الجلسات</th>
                                        <th>تاريخ البدء</th>
                                        <th>تاريخ الانتهاء</th>
                                        <th>الحالة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($subjects as $subject)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $subject->name }}</td>
                                            <td>{{ Str::limit($subject->description, 50) }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $subject->classes->count() }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $subject->total_sessions }}</span>
                                            </td>
                                            <td>{{ $subject->start_date ? $subject->start_date->format('Y-m-d') : 'غير محدد' }}</td>
                                            <td>{{ $subject->end_date ? $subject->end_date->format('Y-m-d') : 'غير محدد' }}</td>
                                            <td>
                                                    <span class="badge bg-{{ $subject->is_active ? 'success' : 'secondary' }}">
                                                        {{ $subject->is_active ? 'نشط' : 'غير نشط' }}
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
