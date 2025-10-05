{{-- resources/views/teacher/students/show.blade.php --}}
@extends('layouts.app')
@php use Carbon\Carbon; @endphp

@section('title', "تفاصيل الطالب {$profile->first_name} {$profile->last_name}")

@section('content')
    <div class="container-fluid">
        {{-- العنوان وزر العودة --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">تفاصيل الطالب</h1>
            <a href="{{ route('teacher.students.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left"></i> العودة
            </a>
        </div>

        {{-- البطاقة الرئيسية --}}
        <div class="card shadow-sm mb-4">
            <div class="row g-0">
                {{-- عمود الصورة والاسم --}}
                <div class="col-md-4 text-center p-4 border-end">
                    @if($profile->image)
                        <img src="{{ asset('storage/' . $profile->image) }}"
                             alt="صورة الطالب"
                             class="rounded-circle img-fluid"
                             style="max-width:180px; height:180px; object-fit:cover;">
                    @else
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center"
                             style="width:180px; height:180px;">
                            <i class="bi bi-person-fill" style="font-size:4rem; color:#ccc;"></i>
                        </div>
                    @endif

                    <h5 class="mt-3">{{ $profile->first_name }} {{ $profile->last_name }}</h5>
                    <span class="badge bg-primary">{{ $student->email }}</span>

                    {{-- كود QR --}}
                    <div class="mt-3">
                        <div class="border rounded p-2 bg-light d-inline-block">
                            {!! QrCode::size(100)->generate($profile->qr) !!}
                        </div>
                        <p class="mt-1 small text-muted">{{ $profile->qr }}</p>
                    </div>
                </div>

                {{-- عمود البيانات --}}
                <div class="col-md-8">
                    <div class="card-body">
                        <div class="row gy-3">
                            {{-- المعلومات الشخصية --}}
                            <div class="col-12">
                                <h6 class="mb-3 border-bottom pb-2">
                                    <i class="bi bi-person-vcard me-1 text-primary"></i> المعلومات الشخصية
                                </h6>
                            </div>

                            <div class="col-sm-6">
                                <i class="bi bi-phone me-1 text-muted"></i>
                                <strong>الهاتف:</strong>
                                <span>{{ $profile->phone ?? '—' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <i class="bi bi-geo-alt me-1 text-muted"></i>
                                <strong>العنوان:</strong>
                                <span>{{ $profile->address ?? '—' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <i class="bi bi-calendar3 me-1 text-muted"></i>
                                <strong>تاريخ الميلاد:</strong>
                                <span>
                                {{ $profile->birthdate
                                    ? Carbon::parse($profile->birthdate)->format('Y-m-d')
                                    : '—'
                                }}
                            </span>
                            </div>
                            <div class="col-sm-6">
                                <i class="bi bi-person-lines-fill me-1 text-muted"></i>
                                <strong>اسم الأب:</strong>
                                <span>{{ $profile->father_name ?? '—' }}</span>
                            </div>

                            {{-- المعلومات الأسرية --}}
                            <div class="col-12 pt-3 border-top">
                                <h6 class="mb-3">
                                    <i class="bi bi-house-heart me-1 text-success"></i> المعلومات الأسرية
                                </h6>
                            </div>

                            <div class="col-sm-6">
                                <strong>عمل الأب:</strong>
                                <span>{{ $profile->father_job ?? '—' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <strong>عمل الأم:</strong>
                                <span>{{ $profile->mother_job ?? '—' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <strong>اسم المدرسة:</strong>
                                <span>{{ $profile->school_name ?? '—' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <strong>الحالة المادية:</strong>
                                <span>
                                @if($profile->financial_status)
                                        <span class="badge bg-{{ $profile->financial_status == 'مستور' ? 'success' : ($profile->financial_status == 'متوسط' ? 'warning' : 'secondary') }}">
                                        {{ $profile->financial_status }}
                                    </span>
                                    @else
                                        —
                                    @endif
                            </span>
                            </div>

                            {{-- المعلومات التعليمية --}}
                            <div class="col-12 pt-3 border-top">
                                <h6 class="mb-3">
                                    <i class="bi bi-book me-1 text-info"></i> المعلومات التعليمية
                                </h6>
                            </div>

                            <div class="col-sm-6">
                                <strong>عدد الأجزاء المحفوظة:</strong>
                                <span class="badge bg-info">{{ $profile->memorized_parts ?? 0 }} جزء</span>
                            </div>
                            <div class="col-sm-6">
                                <strong>هل لديه أخ مسجل:</strong>
                                <span class="badge bg-{{ $profile->has_sibling ? 'success' : 'secondary' }}">
                                {{ $profile->has_sibling ? 'نعم' : 'لا' }}
                            </span>
                            </div>
                            @if($profile->has_sibling)
                                <div class="col-sm-6">
                                    <strong>عدد الإخوة المسجلين:</strong>
                                    <span>{{ $profile->siblings_count ?? 0 }}</span>
                                </div>
                            @endif

                            {{-- الإحصائيات --}}
                            <div class="col-12 pt-3 border-top">
                                <h6 class="mb-3">
                                    <i class="bi bi-graph-up me-1 text-warning"></i> الإحصائيات
                                </h6>
                            </div>

                            <div class="col-sm-6">
                                <i class="bi bi-graph-up me-1 text-muted"></i>
                                <strong>نسبة الحضور:</strong>
                                <span>
                                <span class="badge bg-{{ $profile->present_percentage >= 75 ? 'success' : ($profile->present_percentage >= 50 ? 'warning' : 'danger') }}">
                                    {{ $profile->present_percentage ?? 0 }}%
                                </span>
                            </span>
                            </div>
                            <div class="col-sm-6">
                                <i class="bi bi-trophy me-1 text-muted"></i>
                                <strong>النقاط:</strong>
                                <span class="badge bg-success">{{ $profile->points ?? 0 }}</span>
                            </div>

                            {{-- بيانات الوصي --}}
                            <div class="col-12 pt-3 border-top">
                                <h6 class="mb-2">
                                    <i class="bi bi-people-fill me-1 text-danger"></i> بيانات الوصي
                                </h6>
                            </div>
                            @if($profile->guardian)
                                <div class="col-sm-6">
                                    <strong>اسم الوصي:</strong>
                                    <span>{{ $profile->guardian->firstname ?? '' }} {{ $profile->guardian->lastname ?? '' }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>هاتف الوصي:</strong>
                                    <span>{{ $profile->guardian->phone ?? '—' }}</span>
                                </div>
                                <div class="col-sm-12">
                                    <strong>عنوان الوصي:</strong>
                                    <span>{{ $profile->guardian->address ?? '—' }}</span>
                                </div>
                            @else
                                <div class="col-12 text-muted">
                                    لا توجد بيانات وصي مسجَّلة لهذا الطالب.
                                </div>
                            @endif

                            {{-- الحالة الصحية --}}
                            @if($profile->health_status)
                                <div class="col-12 pt-3 border-top">
                                    <h6 class="mb-2">
                                        <i class="bi bi-heart-pulse me-1 text-danger"></i> الحالة الصحية
                                    </h6>
                                    <div class="bg-light rounded p-3">
                                        {{ $profile->health_status }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- معلومات إضافية - الحلقات المشتركة --}}
        <div class="row">
            {{-- الحلقات المشتركة --}}
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-journals me-1 text-primary"></i> الحلقات المشتركة
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $teacherClasses = auth()->user()->teacherClasses ?? collect();
                            $studentClasses = $student->educationClasses->whereIn('id', $teacherClasses->pluck('id'));
                        @endphp

                        @if($studentClasses->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($studentClasses as $class)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $class->name }}</h6>
                                            <small class="text-muted">{{ $class->subject->name ?? '—' }}</small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">{{ $class->students_count ?? 0 }} طالب</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">لا توجد حلقات مشتركة</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- الإنجازات الأخيرة --}}
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-star me-1 text-warning"></i> ملخص الأداء
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary mb-1">{{ $profile->present_percentage ?? 0 }}%</h4>
                                    <small class="text-muted">نسبة الحضور</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success mb-1">{{ $profile->points ?? 0 }}</h4>
                                <small class="text-muted">النقاط المكتسبة</small>
                            </div>
                        </div>
                        <hr>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-info mb-1">{{ $profile->memorized_parts ?? 0 }}</h4>
                                    <small class="text-muted">الأجزاء المحفوظة</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-secondary mb-1">{{ $studentClasses->count() }}</h4>
                                <small class="text-muted">عدد الحلقات</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            border: none;
            border-radius: 10px;
        }
        .list-group-item {
            border: none;
            border-bottom: 1px solid #eee;
        }
        .list-group-item:last-child {
            border-bottom: none;
        }
        .border-end {
            border-right: 1px solid #dee2e6 !important;
        }
    </style>
@endsection
