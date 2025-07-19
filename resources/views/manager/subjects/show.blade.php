{{-- resources/views/manager/subjects/show.blade.php --}}
@extends('layouts.app')
@section('title',"تفاصيل المادة — {$subject->name}")

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">تفاصيل المادة: {{ $subject->name }}</h1>
            <div>
                <a href="{{ route('manager.subjects.edit', $subject) }}" class="btn btn-warning btn-sm me-2">
                    <i class="bi bi-pencil-square"></i> تعديل
                </a>
                <a href="{{ route('manager.subjects.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> رجوع
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-primary h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-1">إجمالي الجلسات</h5>
                        <p class="display-6">{{ $subject->total_sessions }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-success h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-1">عدد الاختبارات</h5>
                        <p class="display-6">{{ $subject->exams_count }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-info h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-1">تاريخ البداية</h5>
                        <p class="display-6">{{ $subject->start_date?->format('Y-m-d') ?? '—' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-warning h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-1">تاريخ النهاية</h5>
                        <p class="display-6">{{ $subject->end_date?->format('Y-m-d') ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">معلومات تفصيلية</h5>
            </div>
            <div class="card-body text-end">
                <p><strong>الوصف:</strong> {{ $subject->description ?? '—' }}</p>
                <p><strong>المستوى:</strong> {{ $subject->level ?? '—' }}</p>
                <p><strong>الدرجة:</strong> {{ $subject->degree ?? '—' }}</p>
                <p><strong>الحالة:</strong>
                    @if($subject->is_active)
                        <span class="badge bg-success">نشط</span>
                    @else
                        <span class="badge bg-secondary">معطل</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
@endsection
