{{-- resources/views/super-admin/institutes/show.blade.php --}}

@extends('layouts.app')
@section('title', "تفاصيل المعهد — {$institute->name}")

@section('content')
    <div class="container-fluid">

        {{-- الرأس --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">تفاصيل المعهد: {{ $institute->name }}</h1>
            <div>
                <a href="{{ route('super-admin.institutes.edit', $institute) }}"
                   class="btn btn-warning me-2">
                    <i class="bi bi-pencil-square"></i> تعديل
                </a>
                <a href="{{ route('super-admin.institutes.index') }}"
                   class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> الرجوع للقائمة
                </a>
            </div>
        </div>

        {{-- معلومات أساسية --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    @if($institute->image)
                        <img src="{{ asset('storage/app/public/'.$institute->image) }}"
                             class="card-img-top" alt="شعار المعهد">
                    @else
                        <div class="bg-light p-5 text-center text-muted">
                            لا يوجد شعار
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <p><strong>الاسم:</strong> {{ $institute->name }}</p>
                        <p><strong>العنوان:</strong>
                            {{ $institute->address ?: '—' }}
                        </p>
                        <p><strong>المدير الرئيسي:</strong>
                            {{ optional($institute->manager->admin)->first_name }}
                            {{ optional($institute->manager->admin)->last_name }}
                        </p>
                        <p><strong>حساب المدير :</strong>
                            {{ $institute->manager->email }}
                        </p>
                        <p><strong>تاريخ الإنشاء:</strong>
                            {{ $institute->created_at->translatedFormat('Y-m-d') }}
                        </p>
                        <p><strong>الحالة:</strong>
                            @if($institute->deleted_at)
                                <span class="badge bg-secondary">معطل</span>
                            @else
                                <span class="badge bg-success">نشط</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- إحصائيات بالبطاقات --}}
        <div class="row g-3 mb-5">
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-primary h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-book fs-1 me-3"></i>
                        <div>
                            <h5 class="card-title mb-0">المواد</h5>
                            <p class="card-text display-6">{{ $subjectsCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-success h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-journal-code fs-1 me-3"></i>
                        <div>
                            <h5 class="card-title mb-0">الحلقات</h5>
                            <p class="card-text display-6">{{ $classesCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-warning h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-people fs-1 me-3"></i>
                        <div>
                            <h5 class="card-title mb-0">المديرون الإضافيون</h5>
                            <p class="card-text display-6">{{ $adminsCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-info h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-person-badge fs-1 me-3"></i>
                        <div>
                            <h5 class="card-title mb-0">المعلمون</h5>
                            <p class="card-text display-6">{{ $teachersCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- تفاصيل إضافية: قائمة المواد --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">قائمة المواد ({{ $subjectsCount }})</h5>
            </div>
            <div class="card-body p-0">
                @if($institute->subjects->isEmpty())
                    <p class="text-center text-muted py-4">لا توجد مواد مسجلة بعد.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($institute->subjects as $subject)
                            <li class="list-group-item">
                                {{ $subject->name }}
                                @if($subject->is_active)
                                    <span class="badge bg-success float-end">نشط</span>
                                @else
                                    <span class="badge bg-secondary float-end">غير نشط</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- قائمة الحلقات --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">قائمة الحلقات ({{ $classesCount }})</h5>
            </div>
            <div class="card-body p-0">
                @if($institute->classes->isEmpty())
                    <p class="text-center text-muted py-4">لا توجد حلقات مسجلة بعد.</p>
                @else
                    <table class="table mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>اسم الحلقة</th>
                            <th>المادة</th>
                            <th>عدد الطلاب</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($institute->classes as $cls)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $cls->name }}</td>
                                <td>{{ $cls->subject->name }}</td>
                                <td>{{ $cls->students()->count() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

    </div>
@endsection
