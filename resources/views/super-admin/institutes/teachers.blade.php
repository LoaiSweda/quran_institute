@extends('layouts.app')

@section('title', 'معلمو المعهد - ' . $institute->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header bg-white p-4 rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 text-primary">معلمو المعهد: {{ $institute->name }}</h2>
                            <p class="text-muted mb-0">  جميع معلمي المعهد</p>
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
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>قائمة المعلمين ({{ $teachers->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($teachers->isEmpty())
                            <p class="text-center text-muted p-4">لا يوجد معلمون في هذا المعهد</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>صورة</th>
                                        <th>اسم المعلم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الهاتف</th>
                                        <th>الفصول</th>
                                        <th>العنوان</th>
                                        <th>الحالة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($teachers as $teacher)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if($teacher->image)
                                                    <img src="{{ asset('storage/app/public/' . $teacher->image) }}" alt="{{ $teacher->first_name }}" class="rounded-circle" width="40" height="40">
                                                @else
                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $teacher->first_name }} {{ $teacher->last_name }}</td>
                                            <td>{{ $teacher->user->email ?? 'غير متوفر' }}</td>
                                            <td>{{ $teacher->phone ?? 'غير متوفر' }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $teacher->teachingClasses->count() }}</span>
                                            </td>
                                            <td>{{ $teacher->address ?? 'غير متوفر' }}</td>
                                            <td>
                                                    <span class="badge bg-{{ $teacher->is_active ? 'success' : 'secondary' }}">
                                                        {{ $teacher->is_active ? 'نشط' : 'غير نشط' }}
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
