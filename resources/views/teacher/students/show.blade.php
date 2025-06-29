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
                    <img src="{{ asset('storage/'.$profile->image) }}"
                         alt="صورة الطالب"
                         class="rounded-circle img-fluid"
                         style="max-width:180px;">
                @else
                    <div class="rounded-circle bg-light d-inline-block"
                         style="width:180px; height:180px; line-height:180px;">
                        <i class="bi bi-person-fill" style="font-size:4rem; color:#ccc;"></i>
                    </div>
                @endif

                <h5 class="mt-3">{{ $profile->first_name }} {{ $profile->last_name }}</h5>
                <span class="badge bg-primary">{{ $student->email }}</span>
            </div>

            {{-- عمود البيانات --}}
            <div class="col-md-8">
                <div class="card-body">
                    <div class="row gy-3">
                        {{-- بيانات الطالب --}}
                        <div class="col-sm-6">
                            <i class="bi bi-qr-code me-1 text-muted"></i>
                            <strong>الكود التعريفي:</strong>
                            <span>{{ $profile->qr ?? '—' }}</span>
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
                        <div class="col-sm-6">
                            <i class="bi bi-graph-up me-1 text-muted"></i>
                            <strong>نسبة الحضور:</strong>
                            <span>{{ $profile->present_percentage ?? '—' }}%</span>
                        </div>
                        <div class="col-sm-6">
                            <i class="bi bi-trophy me-1 text-muted"></i>
                            <strong>النقاط:</strong>
                            <span>{{ $profile->points ?? '—' }}</span>
                        </div>

                        {{-- بيانات الوصي --}}
                        <div class="col-12 pt-3 border-top">
                            <h6 class="mb-2">
                                <i class="bi bi-people-fill me-1 text-muted"></i> بيانات الوصي
                            </h6>
                        </div>
                        @if($profile->guardian)
                            <div class="col-sm-6">
                                <strong>اسم الوصي:</strong>
                                <span>{{ $profile->guardian->firstname }} {{ $profile->guardian->lastname }}</span>
                            </div>
                            <div class="col-sm-6">
                                <strong>هاتف الوصي:</strong>
                                <span>{{ $profile->guardian->phone }}</span>
                            </div>
                            <div class="col-sm-12">
                                <strong>عنوان الوصي:</strong>
                                <span>{{ $profile->guardian->address }}</span>
                            </div>
                        @else
                            <div class="col-12 text-muted">
                                لا توجد بيانات وصي مسجَّلة لهذا الطالب.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
